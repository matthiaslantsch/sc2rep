<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Match model
 */

namespace HIS5\sc2rep\models;

use HIS5\lib\activerecord as ar;
use HIS5\lib\Common as co;
use HIS5\sc2rep\helpers as helpers;

/**
 * match model class
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\PHP\sc2rep\models
 */
class MatchModel extends ar\ModelBase {

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $hasMany = ["performances"];

	/**
	 * property containing belongsTo relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $belongsTo = ["status"];

	/**
	 * property containing belongsTo relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $many2many = ["tags"];
	
	/**
	 * property containing verification data for some of the columns
	 *
	 * @access 	public
	 * @var 	array with verification data
	 */
	public static $validate = array(
		"identifier" => ["presence", "length" => ["max" => 255]],
		"isLadder" => ["presence"],
		"loops" => ["presence"],
		"played" => ["presence"]
	);

	/**
	 * getter method to sort the tags like we want to
	 *
	 * @access public
	 * @return array with the corresponding tags ordered by group
	 */
	public function getTags() {
		$ret = [];
		$tags = TagModel::select(["match.idMatch" => $this->id]);
		foreach ($tags as $tag) {
			if(isset($ret[$tag->group])) {
				if(is_array($ret[$tag->group])) {
					$ret[$tag->group][] = $tag;
				} else {
					$cur = $ret[$tag->group];
					$ret[$tag->group] = [$cur, $tag];			
				}
			} else {
				$ret[$tag->group] = $tag;
			}
		}
		return $ret;
	}

	/**
	 * getter method to sort the teams like we want to
	 *
	 * @access public
	 * @return array with the corresponding players ordered by team
	 */
	public function getTeams() {
		$ret = [];
		foreach ($this->performances as $perf) {
			$ret[$perf->team][] = $perf;
		}
		return $ret;
	}

	/**
	 * getter method to return a team string, depending on the team number
	 *
	 * @access public
	 * @param  the team number, starting with one
	 * @return string with the corresponding player's names
	 */
	public function getTeamString($team = 1) {
		$teams = $this->getTeams();
		if(!isset($teams[$team])) {
			return "";
		}

		$ret = "";
		foreach ($teams[$team] as $pl) {
			$ret .= $pl->player->fullname.", ";
		}
		return rtrim($ret, ", ");
	}

	/**
	 * getter method to return a performance object of a player with a certain sid
	 *
	 * @access public
	 * @param  sid int | the sid of the requested player
	 * @return PerformanceModel the player performance model
	 */
	public function getPlayerBySid($sid) {
		return PerformanceModel::get(["idMatch" => $this->id, "sid" => $sid]);
	}

	/**
	 * getter method to get a title string
	 *
	 * @access public
	 * @return title a string
	 */
	public function getTitle() {
		$map = "";
		$gametype = "";

		foreach ($this->tags as $tag) {
			if($tag->group == "map") {
				$map = $tag->name;
			}

			if($tag->group == "gametype") {
				$gametype = $tag->name;
			}
		}

		if($map !== "" && $gametype !== "") {
			return "{$gametype} - {$map}";
		} else {
			return "sc2rep - {$this->id}";
		}
	}

	/**
	 * small helper method constructs a string like 12 minutes ago
	 *
	 * @access public
	 * @return the ago string
	 */
	public function agoString() {
		$dist = time() - strtotime($this->played);
		if($dist >= 86400) {
			return (int)($dist / 86400) . ' days ago';
		} elseif($dist >= 3600) {
			return (int)($dist / 3600) . ' hours';
		} if($dist >= 60) {
			return (int)($dist / 60) . ' minutes ago';
		} else {
			return $dist . ' seconds ago';
		}
	}

	/**
	 * returns the total path of the file, where it should be
	 *
	 * @access public
	 * @return the path where this file should be, considering the standards
	 */
	public function getPath() {
		return co\registry("app.path").DIRECTORY_SEPARATOR."updir".DIRECTORY_SEPARATOR."SC2REP-".$this->id.".SC2replay";
	}

	/**
	 * returns a pack of data from the cache/starts a parsing job to populate the cache if the pack is not avaible
	 * allows only a few datapacks
	 *
	 * @access public
	 * @param  string dataPack | the name of the datapack requested
	 * @return string data | the contents of the requested datapack file
	 */
	public function loadDataPack($dataPack) {
		$cachePath = sys_get_temp_dir().DIRECTORY_SEPARATOR."sc2rep_cache".DIRECTORY_SEPARATOR."{$this->identifier}".DIRECTORY_SEPARATOR;
		if(!file_exists($cachePath.$dataPack.".json")) {
			//chache non existent
			if($this->idStatus == 2) {
				//another parse request has already been sent
				return false;
			} else {
				//start parsing process
				$this->idStatus = 2;
				$this->save();

				try {
					$importer = new helpers\ImportHelper($this->getPath());
					$importer->parse();
				} catch (\Exception $e) {
					die(var_dump($e->getMessage()));
					$this->idStatus = 3;
					$this->save();
					return null;
				}


				//parsing ended
				$this->idStatus = 3;
				$this->save();
			}
		}

		return file_get_contents($cachePath.$dataPack.".json");
	}

}