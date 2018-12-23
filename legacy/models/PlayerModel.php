<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Player model
 */

namespace HIS5\sc2rep\models;

use HIS5\lib\activerecord as ar;
use HIS5\sc2rep\helpers as helpers;

/**
 * player model class
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\models
 */
class PlayerModel extends ar\ModelBase {

	/**
	 * property containing belongsTo relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $belongsTo = [
		"user" => ["forced" => false]
	];

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $hasMany = ["performances"];
	
	/**
	 * property containing verification data for some of the columns
	 *
	 * @access 	public
	 * @var 	array with verification data
	 */
	public static $validate = array(
		"name" => ["presence", "length" => ["max" => 45]],
		"clantag" => ["presence", "length" => ["max" => 10]],
		"url" => ["presence", "length" => ["max" => 255]],
		"bnet" => ["presence", "length" => ["max" => 10]]
	);

	/**
	 * getter method to use a custom query to return average values for the performance stats
	 *
	 * @access public
	 * @return array with average values
	 */
	public function getAverageData() {
		$db = ar\Database::init()->connector;
		$result = $db->queryAll("SELECT SUM(m.length) AS totalTime,
								COUNT(m.idMatch) AS matchCount,
								SUM(perf.SQ) AS spendingSkill,
								SUM(perf.APM) AS apm,
								SUM(case when perf.isWin = 1 then 1 else 0 end) AS winrate,
								perf.playRace
								FROM `performance` perf
								JOIN `match` m USING(`idMatch`)
								WHERE perf.idPlayer = ? AND m.isLadder = 1
								GROUP BY perf.playRace", [$this->id]);

		$ret = ["totalTime" => 0, "matchCount" => 0, "spendingSkill" => 0, "apm" => 0, "winrate" => 0, "races" => []];
		foreach ($result as $raceGroup) {
			$race = TagModel::find($raceGroup["playRace"])->name;
			$ret["races"][$race] = $raceGroup["matchCount"];
			$ret["matchCount"] += $raceGroup["matchCount"];
			$ret["totalTime"] += $raceGroup["totalTime"];
			$ret["spendingSkill"] += $raceGroup["spendingSkill"];
			$ret["apm"] += $raceGroup["apm"];
			$ret["winrate"] += $raceGroup["winrate"];
		}

		if($ret["matchCount"] > 0) {
			$ret["spendingSkill"] /= $ret["matchCount"];
			$ret["apm"] /= $ret["matchCount"];
			$ret["winrate"] = $ret["winrate"] * 100 / $ret["matchCount"];
		} else {
			$ret["spendingSkill"] = 0;
			$ret["apm"]  = 0;
			$ret["winrate"]  = 0;
		}

		return $ret;
	}

	/**
	 * method calling the battle net miner class to update the player bnet information
	 * updates profile data:
	 *  -1v1 league
	 *  -portrait
	 *
	 * @access public
	 */
	public function mineBnetProfile() {
		if($this->bnet != 0) {
			//don't mine for bots
			$miner = new helpers\BnetprofileMiner($this->url);
			$profileData = $miner->mineProfile();
			
			if(isset($profileData["leagues"]["1v1-".$this->name])) {
				$this->curLeague = $profileData["leagues"]["1v1-".$this->name];
			}

			if(isset($profileData["portrait"])) {
				$this->portrait = $profileData["portrait"];
			}

			if(isset($profileData["name"])) {
				$this->name = $profileData["name"];
			}

			if(isset($profileData["clantag"])) {
				$this->clantag = $profileData["clantag"];
			}
		}
	}

	/**
	 * getter method to get the current league, from where it was updated last
	 *
	 * @access public
	 * @return string the league at the time the match
	 */
	public function getCurrentLeague() {
		return !isset($this->curLeague) ? "default" : $this->curLeague;
	}

	/**
	 * helper method constructing a string with the full name
	 *
	 * @access public
	 * @return string full name of the player (with the clan tag)
	 */
	public function getFullname() {
		return ($this->clantag != "" ? "[{$this->clantag}]" : "").$this->name;
	}

	/**
	 * return the portrait if set
	 *
	 * @access public
	 * @return string portrait name for this player
	 */
	public function getPortrait() {
		return !isset($this->portrait) ? "" : $this->portrait;
	}

}