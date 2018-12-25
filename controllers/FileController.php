<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * class file for the FileController controller class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\controllers;

use RuntimeException;
use holonet\holofw\FWLogger;
use holonet\holofw\FWController;
use holonet\sc2rep\models\MatchModel;
use holonet\sc2rep\helpers\ImportHelper;
use holonet\sc2rep\helpers\ParseException;

/**
 * The FileController class
 * dedicated controller for everything about replay files themselves
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\controller
 */
class FileController extends FWController {

	/**
	 * method for the upload ajax action
	 * new convention: exception on try again, message on do not try again
	 *  -> differentiate between processing errors and rejection
	 * POST /upload
	 * AJAX
	 *
	 * @access public
	 * @return yield from the controller method
	 */
	public function upload() {
		if(!$this->request->files->has("replayfile")) {
			throw new RuntimeException("Upload started with no file submitted", 400);
		}

		if(stripos($this->request->files->get("replayfile")["name"], ".sc2replay") !== false) {
			try {
				$importer = new ImportHelper($this->request->files->get("replayfile")["tmp_name"]);
				yield "error" => "";
				yield "idMatch" => $importer->process();
			} catch (ParseException $e) {
				FWLogger::error($e->getMessage());
				yield "error" => $e->getMessage();
			}
		} else {
			yield "error" => "uploaded file is not a .SC2replay file";
		}

		$this->respondTo("json")->addCallback(function($data) {
			$ret = ["error" => $data["error"]];
			if(isset($data["idMatch"])) {
				$ret["idMatch"] = $data["idMatch"];
			}
			return $ret;
		});
	}

	/**
	 * method for the download action (allows a user to download a replay file)
	 * GET /matches/[idMatch:i]/download
	 *
	 * @access public
	 * @param  int $idMatch The id of the replay to be downloaded
	 * @return yield from the controller method
	 */
	public function download(int $idMatch) {
		$match = MatchModel::get([
			"idMatch" => $idMatch,
			"idStatus[!]" => 1
		]);

		if($match === null) {
			$this->notFound("match with the id '{$idMatch}'");
		}

		if(!file_exists($match->getPath())) {
			$this->notFound("replay file '{$match->getPath()}'");
		}

		$filename =
			strip_tags("SC2REP {$match->id} - {$match->title} - {$match->getTeamString(1)} vs {$match->getTeamString(2)}.SC2replay");
		$this->offerFile($match->getPath(), $filename)->setMimeType("application/sc2replay");
	}

}
