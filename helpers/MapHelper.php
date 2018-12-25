<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the MapHelper map parser class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\helpers;

use holonet\common as co;
use holonet\sc2rep\models\MapModel;
use holonet\common\error\BadEnvironmentException;

/**
 * helper class used to process map info files
 *  -can download minimap images from tl
 *  -can process minimap tga data from the map.py script
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\helpers
 */
class MapHelper {

	/**
	 * property containing the battle.net depot url of the map to process
	 *
	 * @access public
	 * @var    string $depotUrl The url of the map file
	 */
	public $depotUrl;

	/**
	 * property containing the path to the temporary copy of the s2ma file
	 *
	 * @access public
	 * @var    string $temps2ma The path to the temp downloaded map file
	 */
	public $temps2ma;

	/**
	 * property containing the map object after processing
	 *
	 * @access public
	 * @var    MapModel map The actual map object, either with a denied status or not
	 */
	public $map;

	/**
	 * constructor method
	 * already executes parsing to populate data attributes
	 *
	 * @access public
	 * @param  string $identifier The unique map identifier "{battle.net map hash}{region}"
	 * @param  string $url The battle.net depot url where the map can be found
	 * @return void
	 */
	public function __construct($identifier, $url) {
		if(co\registry('pythonExe') === false) {
			throw new BadEnvironmentException('The python executable path was not specified');
		}

		//@TODO this code will break in single setup
		$mapDir = co\registry("app.publicdir").DIRECTORY_SEPARATOR."sc2rep".DIRECTORY_SEPARATOR."gfx".DIRECTORY_SEPARATOR."maps";
		if(!file_exists($mapDir)) {
			mkdir($mapDir);
		}

		$this->depotUrl = $url;
		$this->map = new MapModel([
			"identifier" => $identifier,
			"denied" => false,
			"group" => "map",
			"name" => $identifier
		]);

		//check if the map needs to be downloaded, if not skip
		$this->downloadS2ma();

		if($this->map->denied !== true) {
			$json = exec(co\registry('pythonExe').' '.co\registry('app.path')."/lib/map.py {$this->temps2ma} 2>&1", $out, $retcode);
			if($retcode != 0) {
				die(var_dump($out));
				$this->map->denied = true;
			} else {
				$det = json_decode($json, true);

				if($det["denied"]) {
					$this->map->denied = true;
				} else {
					$this->map->tag->name = $det["name"];
					$this->map->sizeX = $det["mapX"];
					$this->map->sizeY = $det["mapY"];

					if(function_exists("imagecreatetruecolor")) {
						//gd library here
						$this->processMinimapTga(base64_decode($det["minimap"]));
					} else {
						//sadly download from tl.net
						$this->minimapFromTl();
					}
				}
			}
		}

		//unlink($this->temps2ma);
		$this->map->tag->save();
		$this->map->save();
	}

	/**
	 * method attempting to download a s2ma map file (will skip over if already exists in cache)
	 *
	 * @access private
	 * @return void
	 */
	private function downloadS2ma() {
		$this->temps2ma = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR."{$this->map->identifier}.s2ma";
		if(file_exists($this->temps2ma)) {
			//the map is already in the cache
			return;
		}

		if($this->depotUrl === null) {
			$this->map->denied = true;
		}

		$s2maFile = HttpGetter::request($this->depotUrl);
		if($s2maFile === false) {
			$this->map->denied = true;
		}

		file_put_contents($this->temps2ma, $s2maFile);
	}

	/**
	 * method processing a minimap tga file, that is being parsed out of the s2ma
	 *  -first saves it temporarily
	 *  -creates a image ressource
	 *  -crop it (black borders)
	 *  -resize it (map size/2x map size (if y side doesn't get over 200 after doubling))
	 *  -save it as png in the maps folder
	 *
	 * @access private
	 * @param  string $tgaByteString  Byte string code with the minimap in it
	 * @return void
	 */
	private function processMinimapTga($tgaByteString) {
		$tgaFile = realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR."{$this->map->identifier}_sc2rep.tga";
		file_put_contents($tgaFile, $tgaByteString);

		//create image ressource using functions.php imagecreatefromtga()
		require_once "functions.php";
		$imgMap = \imagecreatefromtga($tgaFile);

		//delete the tga file from the cache
		unlink($tgaFile);

		//crop away the dark border
		//could fail so make sure we're save
		$croppedImage = imagecropauto($imgMap, IMG_CROP_BLACK);
		if($croppedImage !== false) {
			$imgMap = $croppedImage;
		}

		//let's double the size to make the map visible
		$resizedMap = imagecreatetruecolor($this->map->sizeX * 2, $this->map->sizeY * 2);
		imagecopyresampled($resizedMap, $imgMap, 0, 0, 0, 0, $this->map->sizeX * 2, $this->map->sizeY * 2, imagesx($imgMap), imagesy($imgMap));

		imagejpeg($resizedMap, $this->map->minimapPath());
	}

	/**
	 * method attempting to download a map image from teamliquid
	 *
	 * @access private
	 * @return void
	 */
	private function minimapFromTl() {
		$tlUrl = "http://wiki.teamliquid.net/starcraft2/".str_replace(' ', '_', $this->map->tag->name);

		$response = HttpGetter::request($tlUrl);
		if($response === false) {
			return;
		}

		$simpleXml = simplexml_load_string($response);

		if($simpleXml === false) {
			return;
		}

		$imgTag = $simpleXml->xpath("//*[@class='image']/img");
		if(isset($imgTag[0])) {
			$imgUrl = $imgTag[0]->attributes()->src;

			$image = HttpGetter::request($imgUrl, null);
			if($image !== false) {
				file_put_contents($this->map->minimapPath(), $image);
			}
		}
	}

}
