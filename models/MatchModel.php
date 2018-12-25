<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * Model class for the MatchModel model class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\models;

use holonet\common as co;
use holonet\activerecord\ModelBase;
use holonet\sc2rep\models\PerformanceModel;
use holonet\sc2rep\helpers\ImportHelper;

/**
 * MatchModel to wrap around the match table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\models
 */
class MatchModel extends ModelBase {

	/**
	 * contains relationship mapping for hasMany
	 *
	 * @access public
	 * @var    array $hasMany Array with definitions for a has many relationship
	 */
	public static $hasMany = array("performances");

	/**
	 * contains relationship mapping for belongsTo
	 *
	 * @access public
	 * @var    array $belongsTo Array with definitions for a belongs to relationship
	 */
	public static $belongsTo = array("status");

	/**
	 * property containing many2many relationship mappings
	 *
	 * @access public
	 * @var    array $many2many Array with relationship mappings
	 */
	public static $many2many = array("tags");

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var    array $validate Array with verification data
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
	public function tagsToArray() {
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
	 * @param  int $team The team number, starting with one
	 * @return string with the corresponding player's names
	 */
	public function getTeamString(int $team = 1) {
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
	 * @param  integer $sid the sid of the requested player
	 * @return PerformanceModel the player performance model
	 */
	public function getPlayerBySid(int $sid) {
		return PerformanceModel::get(["idMatch" => $this->id, "sid" => $sid]);
	}

	/**
	 * getter method to get a title string
	 *
	 * @access public
	 * @return title a title string for this match
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
			return (int)($dist / 3600) . ' hours ago';
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
		return co\filepath(co\registry("app.vardir"), "updir", "SC2REP-".$this->id.".SC2replay");
	}

	/**
	 * returns a pack of data from the cache/starts a parsing job to populate the cache if the pack is not avaible
	 * allows only a few datapacks
	 *
	 * @access public
	 * @param  string $dataPack The name of the datapack requested
	 * @return string $data The contents of the requested datapack file
	 */
	public function loadDataPack($dataPack) {
		$dataPackFile = co\filepath(sys_get_temp_dir(), "sc2rep_cache", $this->identifier, "{$dataPack}.json");
		if(!file_exists($dataPackFile)) {
			//chache non existent
			if($this->idStatus == 2) {
				//another parse request has already been sent
				return false;
			} else {
				//start parsing process
				$this->idStatus = 2;
				$this->save();

				try {
					$importer = new ImportHelper($this->getPath());
					$importer->parse();
				} catch (\Exception $e) {
					$this->idStatus = 3;
					$this->save();
					return null;
				}


				//parsing ended
				$this->idStatus = 3;
				$this->save();
			}
		}

		return file_get_contents($dataPackFile);
	}

}
