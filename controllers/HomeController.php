<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * Class file for the HomeController
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\controllers;

use holonet\holofw\FWController;
use holonet\sc2rep\models\TagModel;
use holonet\sc2rep\models\SeasonModel;
use holonet\sc2rep\models\PlayerModel;
use holonet\sc2rep\models\PerformanceModel;
use holonet\sc2rep\helpers\MatchFilter;

/**
 * HomeController giving the user access to certain general purpose pages
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\controllers
 */
class HomeController extends FWController {

	/**
	 * GET / (root homepage)
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function index() {
		yield "title" => "SC2REP replay analysis tool";
	}

	/**
	 * method for the tag action
	 * GET /tag/[query:]
	 *
	 * @access public
	 * @param  string $query The tag query to display a page for
	 * @return the yield from the controller method
	 */
	public function tag(string $query) {
		if(($tag = TagModel::get("name" => $query) === null) {
			$this->notFound("Could not find tag with the name '{$query}'");
		}

		yield "title" => "SC2REP - {$tag->name}";
		yield "tag" => $tag;
		yield "seasons" => SeasonModel::all();

		$this->format("html")->append("match".DIRECTORY_SEPARATOR."view");
	}

	/**
	 * method for the player action
	 * GET player/[idPlayer:i]
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
	}

	/**
	 * method for the player profile json action
	 * GET /profile/[idPlayer:i] JSON
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

		die("no £_GET");
		$matches = MatchFilter::resolveGET(100, ($pager - 1) * 10);
		$statistics = ["apm" => [], "spending" => [], "winRate" => []];
		$retMatches = [];
		$avgVals = ["apm" => [], "spending" => [], "winRate" => []];
		$recentVals = ["apm" => [], "spending" => [], "winRate" => []];

		foreach (array_reverse($matches) as $i => $match) {
			$perf = PerformanceModel::get(["idMatch" => $match->id, "idPlayer" => $player->id]);
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
					"length" => co\readableTimestring($match->duration),
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

		yield "statistics" => $chartData,
		yield "matches" => array_reverse($retMatches),
		yield "statsData" => $statsData,
		yield "sampler" => $sampler
		$this->format("json");
	}

	/**
	 * api method to get a list of maps
	 * ANY /api/maps
	 * AJAX
	 *
	 * @access public
	 * @return the yield from the controller method
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

}
