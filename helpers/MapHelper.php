<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the MapHelper map parser class
 */

namespace HIS5\sc2rep\helpers;

use HIS5\holoFW\core\error as error;
use HIS5\lib\Common as co;
use HIS5\sc2rep\models as models;

/**
 * helper class used to process map info files
 *  -can download minimap images from tl
 *  -can process minimap tga data from the map.py script
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\helpers
 */
class MapHelper {

	/**
	 * property containing the battle.net depot url of the map to process
	 *
	 * @access 	public
	 * @var 	string depotUrl
	 */
	public $depotUrl;

	/**
	 * property containing the path to the temporary copy of the s2ma file
	 *
	 * @access 	public
	 * @var 	string temps2ma
	 */
	public $temps2ma;

	/**
	 * property containing the map object after processing
	 *
	 * @access 	public
	 * @var 	MapModel map | the actual map object, either with a denied status or not
	 */
	public $map;

	/**
	 * constructor method
	 * already executes parsing to populate data attributes
	 *
	 * @access public
	 * @param  string identifier | the unique map identifier "{battle.net map hash}{region}"
	 * @param  string url | the battle.net depot url where the map can be found
	 */
	public function __construct($identifier, $url) {
		if(co\registry('pythonExe') === false) {
			throw new error\ConfigException('The python executable path was not specified');
		}

		$mapDir = co\registry("app.path").DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."gfx".DIRECTORY_SEPARATOR."maps";
		if(!file_exists($mapDir)) {
			mkdir($mapDir);
		}

		$this->depotUrl = $url;
		$this->map = new models\MapModel([
			"identifier" => $identifier,
			"denied" => false,
			"tag" => new models\TagModel(["group" => "map", "name" => $identifier])
		]);

		//check if the map needs to be downloaded, if not skip
		$this->downloadS2ma();

		if($this->map->denied !== true) {
			$json = exec(co\registry('pythonExe').' '.co\registry('app.path')."/lib/map.py '{$this->temps2ma}' 2>&1", $out, $retcode);
			if($retcode != 0) {
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
	 */
	private function downloadS2ma() {
		$this->temps2ma = sys_get_temp_dir().DIRECTORY_SEPARATOR."{$this->map->identifier}.s2ma";
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
	 * @param  tgaByteString string | byte string code with the minimap in it
	 */
	private function processMinimapTga($tgaByteString) {
		$tgaFile = sys_get_temp_dir().DIRECTORY_SEPARATOR."{$this->map->identifier}_sc2rep.tga";
		file_put_contents($tgaFile, $tgaByteString);

		//create image ressource using lib/functions.php imagecreatefromtga()
		$imgMap = \imagecreatefromtga($tgaFile);

		//delete the tga file from the cache
		unlink($tgaFile);

		//crop away the dark border
		$imgMap = imagecropauto($imgMap, IMG_CROP_BLACK);

		//let's double the size to make the map visible
    	$resizedMap = imagecreatetruecolor($this->map->sizeX * 2, $this->map->sizeY * 2);
    	imagecopyresampled($resizedMap, $imgMap, 0, 0, 0, 0, $this->map->sizeX * 2, $this->map->sizeY * 2, imagesx($imgMap), imagesy($imgMap));

		imagejpeg($resizedMap, $this->map->minimapPath());
	}

	/**
	 * method attempting to download a map image from teamliquid
	 *
	 * @access private
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