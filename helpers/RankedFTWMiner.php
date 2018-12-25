<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the custom recoverable exception
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\helpers;

/**
 * helper class used to crawl rankedftw.com for information
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\helpers
 */
class RankedFTWMiner {

	/**
	 * static property containing the mappings from rankedftw leagueids to actual league strings
	 *
	 * @access public
	 * @var    array $leagueLookup LeagueId lookup table
	 */
	private static $leagueLookup = [
		0 => "bronze",
		1 => "silver",
		2 => "gold",
		3 => "platinum",
		4 => "diamond",
		5 => "master",
		6 => "grandmaster"
	];

	/**
	 * method returning a ranking for a certain team
	 * team being e.g. 1v1, archon, team-2v2, random-2v2 usw...
	 * if no timestamp is given, the most recent ranking is returned
	 *
	 * @access public
	 * @param  string $teamUrl A get url parameter string with the mode and the players in it
	 * @param  integer $timestamp A timestamp for when the ranking is searched for
	 * @return array with player ranking: ["league", "globalRank", "serverRank", "leagueRank"] or null on not found
	 */
	public static function getRanking($teamUrl, $timestamp = null) {
		if($timestamp === null) {
			$timestamp = time();
		}

		$teamId = self::getTeamId($teamUrl);
		if($teamId === null) {
			return null;
		}

		$rankings = self::loadRankings($teamId);
		ksort($rankings);

		//check if the timestamp is newer then the last set of data
		end($rankings); // move the internal pointer to the end of the array
		if(key($rankings) < $timestamp) {
			//just use the last dataset
			return array_pop($rankings);
		}

		//check if the timestamp is older than the oldest set of data
		reset($rankings); // move the internal pointer to the start of the array
		if(key($rankings) > $timestamp) {
			//no dataset that old is avaible
			return null;
		}

		//iterate over the array to find the closest match
		$last = null;
		foreach ($rankings as $ts => $rank) {
			if($ts < $timestamp) {
				$last = $rank;
			}

			if($ts > $timestamp) {
				return $last;
			}
		}

		//failsave, should never get here anyway
		return null;
	}

	/**
	 * method using the /team/id api backend of rankedftw.com to figure out the rankedftw internal team id for a certain team
	 * team being e.g. 1v1, archon, team-2v2, random-2v2 usw...
	 *
	 * @access private
	 * @param  string $mode String for the gamemode (1v1, 2v2, usw...)
	 * @return integer team_id/boolean false returns either the found team_id or false on not found
	 */
	private static function getTeamId($teamUrl) {
		$response = HttpGetter::request("http://rankedftw.com/team/id/".$teamUrl, null);
		if($response === false) {
			return false;
		}

		$answer = json_decode($response, true);
		if(isset($answer["team_id"])) {
			return $answer["team_id"];
		} else {
			return false;
		}
	}

	/**
	 * method using the  /team/{team_id}/rankings/ backend of rankedftw.com to get a json array with the past rankings for that specific team
	 * team_id being the internal team_id in the rankedftw database
	 *
	 * @access private
	 * @param  integer teamId Internal team_id in the rankedftw database
	 * @return array with rankings for that team, ordered by timestamp
	 */
	private static function loadRankings($teamId) {
		$response = HttpGetter::request("http://rankedftw.com/team/".$teamId."/rankings", null);
		if($response === false) {
			return [];
		}

		$answer = json_decode($response, true);
		if($answer === null) {
			return array();
		}

		$ret = [];
		foreach ($answer as $oldRanking) {
			$ret[$oldRanking["data_time"]] = [
				"league" => self::$leagueLookup[$oldRanking["league"]],
				"divisionRank" => $oldRanking["ladder_rank"]." / ".$oldRanking["ladder_count"],
				"leagueRank" => $oldRanking["league_rank"]." / ".$oldRanking["league_count"],
				"serverRank" => $oldRanking["region_rank"]." / ".$oldRanking["region_count"],
				"globalRank" => $oldRanking["world_rank"]." / ".$oldRanking["world_count"],
				"points" => $oldRanking["points"],
				"winrate" => floor(($oldRanking["wins"] / ($oldRanking["losses"] + $oldRanking["wins"])) * 100)
			];
		}

		return $ret;
	}

}
