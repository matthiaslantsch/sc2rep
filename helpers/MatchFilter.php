<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * class file for the MatchFilter filter resolver class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\helpers;

use holonet\sc2rep\models\MatchModel;

/**
 * helper class used to resolve the match filters submitted by GET
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\helpers
 */
class MatchFilter {

	/**
	 * method resolving the via get submitted parameters and returning a result set of matches
	 *
	 * @access public
	 * @param  array $tags The tags submitted by $_GET
	 * @param  int $count The number of matches requested
	 * @param  int $offset The scrolling offset
	 * @return array of matches matching the filter
	 */
	public static function resolveGET(array $tags = array(), int $count = 10, int $offset = 0) {
		$idsNotMatches = [];
		$idsYesMatches = [];
		if(isset($tags["map"])) {
			$idsYesMatches["map"] = MatchModel::select([
				"SELECT" => "idMatch",
				"tag.group" => "map",
				"tag.name" => $tags["map"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($tags["isVSAi"])) {
			$idsNotMatches += MatchModel::select([
				"SELECT" => "idMatch",
				"tag.name" => "'vs AI"
			]);
		}

		if(isset($tags["league"])) {
			foreach ($tags["league"] as $i => $league) {
				$tags["league"][$i] = ucfirst($league);
			}
			$idsYesMatches["league"] = MatchModel::select([
				"SELECT" => "idMatch",
				"tag.group" => "league",
				"tag.name" => $tags["league"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($tags["proGames"])) {
			$idsYesMatches["proGames"] = MatchModel::select([
				"SELECT" => "idMatch",
				"tag.group" => "player"
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($tags["season"])) {
			$idsYesMatches["season"] = MatchModel::select([
				"SELECT" => "idMatch",
				"tag.idTag" => $tags["season"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($tags["matchup"])) {
			$idsYesMatches["matchup"] = MatchModel::select([
				"SELECT" => "idMatch",
				"tag.name" => $tags["matchup"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($tags["other"])) {
			$idsYesMatches["other"] = MatchModel::select([
				"SELECT" => "idMatch",
				"tag.name" => $tags["other"]
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($tags["team1"]) || isset($tags["team2"])) {
			if(isset($tags["team1"]) && isset($tags["team2"])) {
				sort($tags["team1"]);
				sort($tags["team2"]);

				$likeArr[] = implode("%", $tags["team1"]);
				$likeArr[] = implode("%", $tags["team2"]);
				sort($likeArr);
				$like = "%".implode("%v%", $likeArr)."%";
			} elseif(isset($tags["team1"])) {
				sort($tags["team1"]);
				$like = "%".implode("%", $tags["team1"])."%";
			} elseif(isset($tags["team2"])) {
				sort($tags["team2"]);
				$like = "%".implode("%", $tags["team2"])."%";
			}

			$idsYesMatches["team"] = MatchModel::select([
				"SELECT" => "idMatch",
				"tag.name[~]" => $like,
				"tag.group" => "matchup"
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($tags["player1"])) {
			$idsYesMatches["player1"] = MatchModel::select([
				"SELECT" => "idMatch",
				"performance.idPlayer" => $tags["player1"],
			]);

			//none can be found
			if(empty($idsYesMatches)) {
				return [];
			}
		}

		if(isset($tags["player2"])) {
			$idsYesMatches["player2"] = MatchModel::select([
				"SELECT" => "idMatch",
				"performance.idPlayer" => $tags["player2"],
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
			if(count($idsYesMatches) > 1) {
				$idsYesMatches = call_user_func_array('array_intersect', $idsYesMatches);
			} else {
				$idsYesMatches = array_shift($idsYesMatches);
			}
			$queryOptions["idMatch"] = $idsYesMatches;
		}

		if(!empty($idsNotMatches)) {
			$queryOptions["idMatch[!]"] = $idsNotMatches;
		}

		return MatchModel::select($queryOptions);
	}

}
