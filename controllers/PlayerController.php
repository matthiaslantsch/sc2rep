<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the PlayerController controller class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\controllers;

use holonet\common as co;
use holonet\holofw\FWController;
use holonet\sc2rep\models\PerformanceModel;
use holonet\sc2rep\models\PlayerModel;
use holonet\sc2rep\models\SeasonModel;
use holonet\sc2rep\helpers\MatchFilter;

/**
 * The PlayerController class wraps around the performance/player models
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */
class PlayerController extends FWController {

	/**
	 * method for the player action
	 * GET /player/[idPlayer:i]
	 *
	 * @access public
	 * @param  int $idPlayer The id of the player to show the profile for
	 * @return the yield from the controller method
	 */
	public function player(int $idPlayer) {
		if(($player = PlayerModel::find($idPlayer)) === null) {
			$this->notFound("player with the id '{$idPlayer}'");
		}

		yield "player" => $player;
		yield "profileData" => $player->getAverageData();
		yield "seasons" => SeasonModel::all();
		yield "title" => "SC2REP - {$player->name}";
		$this->renderTemplate(
			"player".DIRECTORY_SEPARATOR."profile",
			"match".DIRECTORY_SEPARATOR."filter"
		);
	}

	/**
	 * method for the player profile json action
	 * GET /profile/[idPlayer:i]/profileData JSON
	 *
	 * @access public
	 * @param  int $idPlayer The id of the player to show the profile for
	 * @return the yield from the controller method
	 */
	public function profileData(int $idPlayer) {
		if(($player = PlayerModel::find($idPlayer)) === null) {
			$this->notFound("player with the id '{$idPlayer}'");
		}

		if(!$this->request->query->has("pager")) {
			$pager = 1;
		} else {
			$pager = $this->request->query->get("pager");
		}

		$matches = MatchFilter::resolveGET(
			$this->request->query->get("tags", array()),
			100, ($pager - 1) * 100
		);
		$statistics = ["apm" => [], "spending" => [], "winRate" => []];
		$retMatches = [];
		$avgVals = ["apm" => [], "spending" => [], "winRate" => []];
		$recentVals = ["apm" => [], "spending" => [], "winRate" => []];

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
		foreach (array_reverse($matches) as $i => $match) {
			$perf = PerformanceModel::get(["idMatch" => $match->id, "idPlayer" => $player->id]);
			if(!is_object($perf)) {
				die(var_dump($perf, $match));
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
				$tags = $match->tagsToArray();
				$retMatches[] = [
					"idMatch" => $match->id,
					"map" => $tags["map"]->name,
					"matchup" => $tags["matchup"]->name,
					"type" => $tags["gametype"]->name,
					"length" => co\readableDurationString($match->length),
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


		yield "keys" => array(
			"#", "MAP", "TYPE", "MATCHUP", "LENGTH", "PLAYED", "APM", "SPENDING SKILL", "RESULT"
		);
		yield "statistics" => $chartData;
		yield "matches" => array_reverse($retMatches);
		yield "statsData" => $statsData;
		yield "sampler" => $sampler;
		$this->respondTo("json");
	}

	/**
	 * api method to get a list of players
	 * can be used with either battle.net id, battle.net url or player name
	 * GET /api/players?term
	 * AJAX
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function playersApi() {
		$term = $searchterm = $this->request->query->get("term", "");
		if(isset($term) && $term !== "" && $term != "0") {
			if(is_numeric($term)) {
				//it's a bnet id
				$result = PlayerModel::select(["bnet[~]" => $term]);
			} elseif(strpos($term, "http://") !== false) {
				//it's a bnet url
				$result = PlayerModel::select(["url[~]" => $term]);
			} else {
				//it's just a name
				$result = PlayerModel::select(["name[~]" => $term]);
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

		$this->respondTo("json")->addCallback(function($data) {return $data["players"];});
	}

}
