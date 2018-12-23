<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the FileController controller class
 */

namespace HIS5\sc2rep\controllers;

use HIS5\holoFW\core as core;
use HIS5\sc2rep\models as models;
use HIS5\sc2rep\helpers as helpers;
use HIS5\lib\Common as co;

/**
 * The FileController class
 * dedicated controller for everything about replay files themselves
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\controllers
 */
class FileController extends core\baseclasses\ControllerBase {

	/**
	 * method for the upload ajax action
	 * new convention: exception on try again, message on do not try again
	 *  -> differentiate between processing errors and rejection
	 * /upload POST
	 * AJAX
	 *
	 * @access public
	 */
	public function upload() {
		if(!isset($_FILES["replayfile"])) {
			throw new core\error\HFWException("Upload started with no file submitted", 400);
		}

		if(stripos($_FILES["replayfile"]["name"], ".sc2replay") !== false) {
			try {
				$importer = new helpers\ImportHelper($_FILES["replayfile"]["tmp_name"]);
				yield "error" => "";
				yield "idMatch" => $importer->process();
			} catch (helpers\ParseException $e) {
				co\Logger::error($e->getMessage());
				yield "error" => $e->getMessage();
			}
		} else {
			yield "error" => "uploaded file is not a .SC2replay file";
		}

		$this->format("json")->addCallback(function($data) {
			$ret = ["error" => $data["error"]];
			if(isset($data["idMatch"])) {
				$ret["idMatch"] = $data["idMatch"];
			}
			return $ret;
		});
	}

	/**
	 * method for the download action (allows a user to download a replay file)
	 * download/idMatch:int ANY
	 *
	 * @access public
	 * @param  array params | array with parameters from the routing process
	 */
	public function download($params = []) {
		$match = models\MatchModel::get([
			"idMatch" => $params['idMatch'],
			"idStatus[!]" => 1
		]);

		if($match === null) {
			throw new core\error\NotFoundException("Could not find match with the id {$params['idMatch']}");
		}

		if(!file_exists($match->getPath())) {
			throw new core\error\NotFoundException("Could not find replay file {$match->getPath()}");
		}

		$filename =
			strip_tags("SC2REP {$match->id} - {$match->title} - {$match->getTeamString(1)} vs {$match->getTeamString(2)}.SC2replay");
		header("Content-type: application/sc2replay");
		header("Content-Disposition: attachment; filename=\"{$filename}\"");
		ob_start();
		echo file_get_contents($match->getPath());
		ob_end_flush();
		exit;
	}

}
