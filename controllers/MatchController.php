<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the MatchController controller class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\controllers;

use holonet\common as co;
use holonet\holofw\FWController;
use holonet\sc2rep\models\MatchModel;
use holonet\sc2rep\models\PerformanceModel;
use holonet\sc2rep\models\TagModel;
use holonet\sc2rep\models\SeasonModel;
use holonet\sc2rep\helpers\MatchFilter;

/**
 * The MatchController class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */
class MatchController extends FWController {

	/**
	 * method for the showMatch action
	 * GET /matches/[idMatch:i]
	 *
	 * @access public
	 * @param  int $idMatch The id of the match to show
	 * @return yield from the controller method
	 */
	public function show(int $idMatch) {
		if(($match = MatchModel::find($idMatch)) === null) {
			$this->notFound("match with the id {$idMatch}");
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

		$this->renderTemplate("match".DIRECTORY_SEPARATOR."show");
		$this->respondTo("json")->addCallback(function($data) {
			$tags = $data["match"]->tagsToArray();
			return [
				"players" => "{$data["match"]->getTeamString(1)} vs {$data["match"]->getTeamString(2)}",
				"matchup" => "{$tags["gametype"]->__toArray("name")} - {$tags["matchup"]->name}",
				"map" => "{$tags["map"]->name}"
			];
		});
	}

	/**
	 * method for the matches action
	 * GET /matches
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function matches() {
		yield "title" => "SC2REP - Starcraft II matches";
		yield "seasons" => SeasonModel::all();
		$this->renderTemplate("match".DIRECTORY_SEPARATOR."filter");
	}

	/**
	 * method for the loadData json action
	 * returns a pack of data from the cache
	 * GET /matches/[idMatch:i]/[dataPack:]
	 * AJAX
	 *
	 * @access public
	 * @param  int $idMatch The id of the match to show
	 * @param  string $dataPack The name of the datapack requested
	 * @return yield from the controller method
	 */
	public function loadData(int $idMatch, string $dataPack) {
		if(!in_array($dataPack, [
			"details", "incomeChartMin", "incomeChartGas", "workerCountChart",
			"resLostChart", "resKilledArmyChart", "spendTechChart", "msg",
			"structuresGraph", "unitsGraph", "apmChart", "armyValChart", "baseCountChart"])
			&& strpos($dataPack, "advStats_") === false) {
			//datapack is not supported
			$this->notFound("Backend does not support data pack {$dataPack}");
		}

		if(($match = MatchModel::get([
			"idMatch" => $idMatch,
			"idStatus[!]" => 1
		])) === null) {
			$this->notFound("match with the id {$idMatch}");
		}

		yield "match" => $match;

		$data = $match->loadDataPack($dataPack);
		if($data === false) {
			yield "error" => true;
		} else {
			yield "dataPack" => $data;

			if(strpos($dataPack, "advStats_") !== false) {
				$playerSid = str_replace("advStats_", "", $dataPack);
				$perf = $match->getPlayerBySid($playerSid);
				if($perf === null) {
					$this->notFound("player with the id {$playerSid}");
				}
				yield "perf" => $perf;

				$dataPack =  "advStats";
			}
		}

		$this->respondTo("html")->tryAppend("match".DIRECTORY_SEPARATOR.$dataPack);
		$this->respondTo("json")->addCallback(function($data) {
			return [
				"error" => isset($data["error"]) ? $data["error"] : false,
				"data" => isset($data["dataPack"]) ? $data["dataPack"] : null
			];
		});
	}

	/**
	 * api method to get a list of matches based on submitted filters
	 * GET /api/matches
	 * AJAX
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function matchApi() {
		$result = MatchFilter::resolveGET(
			$this->request->query->get("tags", array()),
			10, ($this->request->query->get("pager", 1) - 1) * 10
		);

		$ret = [];
		foreach ($result as $i => $match) {
			$tags = $match->tagsToArray();
			$ret[$i] = [
				"idMatch" => $match->id,
				"matchup" => $tags["matchup"]->name,
				"map" => $tags["map"]->name,
				"type" => $tags["gametype"]->name,
				"teamOne" => $match->getTeamString(1),
				"teamTwo" => $match->getTeamString(2),
				"length" => co\readableDurationString($match->length),
				"playedAgo" => $match->agoString()
			];

			if(isset($tags["league"])) {
				if(is_array($tags["league"])) {
					$ret[$i]["leagues"] = "";
					foreach ($tags["league"] as $league) {
						$ret[$i]["leagues"] .= $league->name.", ";
					}
					$ret[$i]["leagues"] = rtrim($ret[$i]["leagues"], ", ");
				} else {
					$ret[$i]["leagues"] = $tags["league"]->name;
				}
			} else {
				$ret[$i]["leagues"] = "";
			}
		}

		yield "matches" => $ret;
		yield "keys" => array(
			"#", "MAP", "TYPE", "MATCHUP", "PLAYERS", "vs PLAYERS", "LEAGUES", "LENGTH", "PLAYED",
		);

		$this->respondTo("json");
	}

}
