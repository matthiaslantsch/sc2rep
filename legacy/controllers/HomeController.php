<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the HomeController controller class
 */

namespace HIS5\sc2rep\controllers;

use HIS5\holoFW\core\error as error;
use HIS5\holoFW\core\baseclasses as base;
use HIS5\sc2rep\models as models;
use HIS5\sc2rep\helpers as helpers;

/**
 * The HomeController class
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\controllers
 */
class HomeController extends base\ControllerBase {

	/**
	 * method for the tag action
	 * /tag/idTag:int ANY
	 *
	 * @access public
	 * @param  array params | array with parameters from the routing process
	 */
	public function tag($params =  []) {
		if(($tag = models\TagModel::find($params['idTag'])) === null) {
			throw new error\NotFoundException("Could not find tag with the id {$params['idTag']}");
		}

		yield "title" => "SC2REP - {$tag->name}";
		yield "tag" => $tag;
		yield "seasons" => models\TagModel::select(["group" => "season"]);

		$this->format("html")->append("match".DIRECTORY_SEPARATOR."view");
	}

	/**
	 * method for the player action
	 * /player/idPlayer:int ANY
	 *
	 * @access public
	 * @param  array params | array with parameters from the routing process
	 */
	public function player($params =  []) {
		if(($player = models\PlayerModel::find($params['idPlayer'])) === null) {
			throw new error\NotFoundException("Could not find player with the id {$params['idPlayer']}");
		}

		yield "player" => $player;
		yield "profileData" => $player->getAverageData();
		yield "seasons" => models\TagModel::select(["group" => "season"]);
		yield "title" => "SC2REP - {$player->name}";
	}

	/**
	 * method for the player profile json action
	 * /profile/idPlayer:int ANY
	 * JSON
	 *
	 * @access public
	 * @param  array params | array with parameters from the routing process
	 */
	public function profileData($params =  []) {
		if(($player = models\PlayerModel::find($params['idPlayer'])) === null) {
			throw new error\NotFoundException("Could not find player with the id {$params['idPlayer']}");
		}

		$matches = helpers\MatchFilter::resolveGET(100, ($_GET["pager"] - 1) * 10);
		$statistics = ["apm" => [], "spending" => [], "winRate" => []];
		$retMatches = [];
		$avgVals = ["apm" => [], "spending" => [], "winRate" => []];
		$recentVals = ["apm" => [], "spending" => [], "winRate" => []];

		foreach (array_reverse($matches) as $i => $match) {
			$perf = models\PerformanceModel::get(["idMatch" => $match->id, "idPlayer" => $player->id]);
			//we need a sampler to condense 
			$sampler = 1;
			if(count($matches) > 10) {
				for ($possibleSampler=1; $possibleSampler <= 10; $possibleSampler++) {
					if(count($matches) % $possibleSampler == 0) {
						$sampler = $possibleSampler;
					}
				}

				if($sampler == 1) {
					//we just set it to 10
					$sampler = 10;
				}
			}

			for ($j = $i; $j <= $i+$sampler-1; $j++) {
				$statistics["apm"][$j][] = intval($perf->APM);
				$statistics["spending"][$j][] = intval($perf->SQ);
				$statistics["winRate"][$j][] = $perf->isWin ? 100 : 0;
			}

			$avgVals["apm"][] = intval($perf->APM);
			$avgVals["spending"][] = intval($perf->SQ);
			$avgVals["winRate"][] = $perf->isWin ? 100 : 0;

			if($i >= count($matches) - 10) {
				$tags = $match->getTags();
				$retMatches[] = [
					"idMatch" => $match->id,
					"map" => $tags["map"]->name,
					"matchup" => $tags["matchup"]->name,
					"type" => $tags["gametype"]->name,
					"length" => base\ViewHelperBase::constructTimeString($match->length),
					"playedAgo" => $match->agoString(),
					"apm" => $perf->APM,
					"spending" => $perf->SQ,
					"result" => $perf->isWin
				];

				if($i >= count($matches) - 5) {
					//save into recent average (last 5 games)
					$recentVals["apm"][] = intval($perf->APM);
					$recentVals["spending"][] = intval($perf->SQ);
					$recentVals["winRate"][] = $perf->isWin ? 100 : 0;
				}
			}
		}

		$chartData = [];
		$statsData = ["all" => [], "recent" => []];
		foreach ($statistics as $key => $stats) {
			if(!empty($stats)) {
				$chartData[$key] = [];
				foreach ($stats as $i => $val) {
					$chartData[$key][] = ["x" => $i, "y" => round(array_sum($val) / count($val))];
				}

				$statsData["all"][$key] = array_sum($avgVals[$key]) / count($avgVals[$key]);
				$statsData["recent"][$key] = array_sum($recentVals[$key]) / count($recentVals[$key]);
			}
		}

		yield "ret" => [
			"statistics" => $chartData,
			"matches" => array_reverse($retMatches),
			"statsData" => $statsData,
			"sampler" => $sampler
		];
		$this->format("json")->addCallback(function($data) {return $data["ret"];});
	}

	/**
	 * api method to get a list of maps
	 * /api/maps ANY
	 * AJAX
	 *
	 * @access public
	 */
	public function mapApi() {
		if(isset($_GET["term"]) && $_GET["term"] !== "") {
			$ret = [];
			foreach(models\MapModel::select(["denied" => false, "tag.name[~]" => $_GET["term"]]) as $map) {
				$ret[] = [
					"id" => $map->tag->id,
					"label" => $map->tag->name,
					"value" => $map->tag->name
				];
			}
			yield "maps" => $ret;
		} else {
			yield "maps" => [];
		}

		$this->format("json")->addCallback(function($data) {return $data["maps"];});
	}

	/**
	 * api method to get a list of players
	 * can be used with either battle.net id, battle.net url or player name
	 * /api/players ANY
	 * AJAX
	 *
	 * @access public
	 */
	public function playersApi() {
		if(isset($_GET["term"]) && $_GET["term"] !== "" && $_GET["term"] != "0") {
			if(is_numeric($_GET["term"])) {
				//it's a bnet id
				$result = models\PlayerModel::select(["bnet[~]" => $_GET["term"]]);
			} elseif(strpos($_GET["term"], "http://") !== false) {
				//it's a bnet url
				$result = models\PlayerModel::select(["url[~]" => $_GET["term"]]);
			} else {
				//it's just a name
				$result = models\PlayerModel::select(["name[~]" => $_GET["term"]]);
			}

			$ret = [];
			foreach($result as $pl) {
				$ret[] = [
					"id" => $pl->id,
					"label" => $pl->fullname,
					"value" => $pl->name
				];
			}
			yield "players" => $ret;
		} else {
			yield "players" => [];
		}

		$this->format("json")->addCallback(function($data) {return $data["players"];});
	}

	/**
	 * api method to get a list of matches based on submitted filters
	 * /api/matches ANY
	 * AJAX
	 *
	 * @access public
	 */
	public function matchApi() {
		yield "result" => helpers\MatchFilter::resolveGET(10, ($_GET["pager"] - 1) * 10);

		$this->format("json")->addCallback(function($data) {
			$ret = [];
			foreach ($data["result"] as $i => $match) {
				$tags = $match->getTags();
				$ret[$i] = [
					"idMatch" => $match->id,
					"matchup" => $tags["matchup"]->name,
					"map" => $tags["map"]->name,
					"type" => $tags["gametype"]->name,
					"teamOne" => $match->getTeamString(1),
					"teamTwo" => $match->getTeamString(2),
					"length" => base\ViewHelperBase::constructTimeString($match->length),
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
			return $ret;
		});
	}

}
