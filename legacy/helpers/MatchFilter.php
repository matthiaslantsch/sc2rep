<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the MatchFilter filter resolver class
 */

namespace HIS5\sc2rep\helpers;

use HIS5\sc2rep\models as models;
use HIS5\lib\Common as co;
use HIS5\holoFW\core\error as error;

/**
 * helper class used to resolve the match filters submitted by GET
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\helpers
 */
class MatchFilter {

	/**
	 * method resolving the via get submitted parameters and returning a result set of matches
	 *
	 * @access public
	 * @param  count int | the number of matches requested
	 * @param  offset int | the scrolling offset
	 */
	public static function resolveGET($count = 10, $offset = 0) {
		$idsNotMatches = [];
		$idsYesMatches = [];
		if(isset($_GET["tags"]["map"])) {
			$idsYesMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"tag.group" => "map",
				"tag.name" => $_GET["tags"]["map"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($_GET["tags"]["isVSAi"])) {
			$idsNotMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"tag.name" => "'vs AI"
			]);
		}

		if(isset($_GET["tags"]["league"])) {
			$idsYesMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"tag.group" => "league",
				"tag.name" => $_GET["tags"]["league"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($_GET["tags"]["proGames"])) {
			$idsYesMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"tag.group" => "player"
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($_GET["tags"]["season"])) {
			$idsYesMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"tag.idTag" => $_GET["tags"]["season"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($_GET["tags"]["matchup"])) {
			$idsYesMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"tag.name" => $_GET["tags"]["matchup"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($_GET["tags"]["other"])) {
			$idsYesMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"tag.name" => $_GET["tags"]["other"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($_GET["tags"]["team1"]) || isset($_GET["tags"]["team2"])) {
			if(isset($_GET["tags"]["team1"]) && isset($_GET["tags"]["team2"])) {
				sort($_GET["tags"]["team1"]);
				sort($_GET["tags"]["team2"]);

				$likeArr[] = implode("%", $_GET["tags"]["team1"]);
				$likeArr[] = implode("%", $_GET["tags"]["team2"]);
				sort($likeArr);
				$like = "%".implode("%v%", $likeArr)."%";
			} elseif(isset($_GET["tags"]["team1"])) {
				sort($_GET["tags"]["team1"]);
				$like = "%".implode("%", $_GET["tags"]["team1"])."%";
			} elseif(isset($_GET["tags"]["team2"])) {
				sort($_GET["tags"]["team2"]);
				$like = "%".implode("%", $_GET["tags"]["team2"])."%";
			}

			$idsYesMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"tag.name[~]" => $like,
				"tag.group" => "matchup"
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($_GET["tags"]["player1"])) {
			$idsYesMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"performance.idPlayer" => $_GET["tags"]["player1"],
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($_GET["tags"]["player2"])) {
			$idsYesMatches += models\MatchModel::select([
				"SELECT" => "idMatch",
				"performance.idPlayer" => $_GET["tags"]["player2"],
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		$queryOptions = [
			"idStatus[!]" => 1,
			"ORDER" => "played",
			"OFFSET" => $offset,
			"LIMIT" => $count
		];

		$idsNo = [];

		if(!empty($idsYesMatches)) {
			foreach ($idsYesMatches as $entry) {
				$idsYes[] = $entry["idMatch"];
			}

			$queryOptions["idMatch"] = $idsYes;
		}

		if(!empty($idsNotMatches)) {
			foreach ($idsNotMatches as $entry) {
				$idsNo[] = $entry["idMatch"];
			}

			$queryOptions["idMatch[!]"] = $idsNo;
		}

		return models\MatchModel::select($queryOptions);
	}

}