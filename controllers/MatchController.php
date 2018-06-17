<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the MatchController controller class
 */

namespace HIS5\sc2rep\controllers;

use HIS5\holoFW\core as core;
use HIS5\sc2rep\models as models;
use HIS5\sc2rep\helpers as helpers;

/**
 * The MatchController class
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\controllers
 */
class MatchController extends core\baseclasses\ControllerBase {

	/**
	 * method for the showMatch action
	 * /match/idMatch:int ANY
	 *
	 * @access public
	 * @param  array params | array with parameters from the routing process
	 */
	public function showMatch($params = []) {
		if(($match = models\MatchModel::find($params['idMatch'])) === null) {
			throw new core\error\NotFoundException("Could not find match with the id {$params['idMatch']}");
		}

		yield "title" => "SC2REP - {$match->getTitle()}";
		yield "match" => $match;

		$playersJson = [];
		foreach ($match->performances as $pl) {
			$playersJson[$pl->sid] = [
				"sid" => $pl->sid,
				"baseTimings" => [],
				"saturationTimings" => []
			];

			if(isset($pl->secondBase)) {
				$playersJson[$pl->sid]["baseTimings"][] = (int)$pl->__get("secondBase");
			}

			if(isset($pl->thirdBase)) {
				$playersJson[$pl->sid]["baseTimings"][] = (int)$pl->__get("thirdBase");
			}

			if(isset($pl->onebaseSat)) {
				$playersJson[$pl->sid]["saturationTimings"][] = (int)$pl->__get("onebaseSat");
			}

			if(isset($pl->twobaseSat)) {
				$playersJson[$pl->sid]["saturationTimings"][] = (int)$pl->__get("twobaseSat");
			}

			if(isset($pl->threebaseSat)) {
				$playersJson[$pl->sid]["saturationTimings"][] = (int)$pl->__get("threebaseSat");
			}

			if(isset($pl->workerCount)) {
				$playersJson[$pl->sid]["workerCount"] = (int)$pl->__get("workerCount");
			}
		}
		yield "playersJson" => $playersJson;

		$this->format("html")->append("match".DIRECTORY_SEPARATOR."showMatch");
		$this->format("json")->addCallback(function($data) {
			$tags = $data["match"]->getTags();
			return [
				"players" => "{$data["match"]->getTeamString(1)} vs {$data["match"]->getTeamString(2)}",
				"matchup" => "{$tags["gametype"]->__toArray("name")} - {$tags["matchup"]->name}",
				"map" => "{$tags["map"]->name}"
			];
		});
	}

	/**
	 * method for the matches action
	 * /matches ANY
	 *
	 * @access public
	 */
	public function matches() {
		$this->format("html")->append("match".DIRECTORY_SEPARATOR."view");
		yield "title" => "SC2REP - Starcraft II matches";
		yield "seasons" => models\TagModel::select(["group" => "season"]);
	}

	/**
	 * method for the loadData json action
	 * returns a pack of data from the cache
	 * /loadData/idMatch:int/dataPack:string ANY
	 * AJAX
	 *
	 * @access public
	 * @param  array params | array with parameters from the routing process
	 */
	public function loadData($params = []) {
		if(!in_array($params["dataPack"], [
			"details", "incomeChartMin", "incomeChartGas", "workerCountChart",
			"resLostChart", "resKilledArmyChart", "spendTechChart", "msg",
			"structuresGraph", "unitsGraph", "apmChart", "armyValChart", "baseCountChart"])
			&& strpos($params["dataPack"], "advStats_") === false) {
			//datapack is not supported
			throw new core\error\NotFoundException("Backend does not support data pack {$params["dataPack"]}");
		}

		$match = models\MatchModel::get([
			"idMatch" => $params['idMatch'],
			"idStatus[!]" => 1
		]);

		if($match === null) {
			throw new core\error\NotFoundException("Could not find match with the id {$params['idMatch']}");
		}

		yield "match" => $match;

		$dataPack = $match->loadDataPack($params["dataPack"]);
		if($dataPack === false) {
			yield "error" => true;
		} else {
			yield "dataPack" => $dataPack;

			if(strpos($params["dataPack"], "advStats_") !== false) {
				$playerSid = str_replace("advStats_", "", $params["dataPack"]);
				$perf = $match->getPlayerBySid($playerSid);
				if($perf === null) {
					throw new core\error\NotFoundException("Could not find player with the id {$playerSid}");
				}
				yield "perf" => $perf;

				$params["dataPack"] =  "advStats";
			}
		}

		$this->format("html")->append("match".DIRECTORY_SEPARATOR.$params["dataPack"])->raw = true;
		$this->format("json")->addCallback(function($data) {
			return [
				"error" => isset($data["error"]) ? $data["error"] : false,
				"data" => isset($data["dataPack"]) ? $data["dataPack"] : null
			];
		});
	}

}
