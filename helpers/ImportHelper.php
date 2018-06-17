<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the ImportHelper logic class
 */

namespace HIS5\sc2rep\helpers;

use HIS5\lib\Common as co;
use HIS5\sc2rep\models as models;
use HIS5\holoFW\core\error as error;
use HIS5\lib\activerecord as ar;

/**
 * helper class used import a replay into the database
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\helpers
 */
class ImportHelper {

	/**
	 * property containing the path of the replay to import
	 *
	 * @access 	public
	 * @var 	string path
	 */
	public $path;

	/**
	 * property containing the output of the identify.py script
	 *   -replay hash
	 *   -map identifier
	 *   -map depot url
	 *   -loop count
	 *
	 * @access 	public
	 * @var 	array identify | the data from the identify.py script
	 */
	public $identify;

	/**
	 * property containing the data array from the parse.py script
	 *
	 * @access 	public
	 * @var 	array data from the parse.py script
	 */
	public $data;

	/**
	 * constructor method
	 *
	 * @access public
	 * @param  string path the file path of the replay to import
	 */
	public function __construct($filepath) {
		$updir = co\registry("app.path").DIRECTORY_SEPARATOR."updir";
		if(!file_exists($updir)) {
			mkdir($updir);
		}

		if(!file_exists($filepath)) {
			throw new error\ConfigException("File {$filepath} does not exist");
		}

		if(co\registry('pythonExe') === false) {
			throw new error\ConfigException('The python executable path was not specified');
		}

		$this->path = $filepath;
		exec(co\registry('pythonExe').' '.co\registry('app.path')."/lib/identify.py {$this->path} 2>&1", $out, $retcode);
		if($retcode != 0) {
			throw new ParseException("the replay file appears to be corrupt [1]", 400);
		}

		$det = json_decode(implode("\n", $out), true);
		if($det["denied"]) {
			throw new ParseException("replay files from coop/campaign/arcade are not supported", 400);
		}

		$this->identify = $det;
	}

	/**
	 * method attempting to process a replay file from a given path
	 *
	 * @access public
	 * @return integer id of the processed match (will throw exception in error cases)
	 */
	public function process() {
		if(($map = models\MapModel::get(["identifier" => $this->identify["mapHash"]])) === null) {
			$maphelper = new MapHelper($this->identify["mapHash"], $this->identify["mapUrl"]);
			$map = $maphelper->map;
		}

		if($map->denied) {
			throw new ParseException("the map the replay was played on was not a melee map", 400);
		}

		//the map is OK, start with the replay parsing
		$possibleDuplicate = models\MatchModel::get(["identifier" => $this->identify["repHash"]]);

		if($possibleDuplicate !== null) {
			if($possibleDuplicate->loops < $this->identify["loops"]) {
				//this replay might contain more information about the same match
				$this->parse();
				$this->update($possibleDuplicate);
			}

			return $possibleDuplicate->id;
		} else {
			$this->parse();
			return $this->import();
		}
	}

	/**
	 * helper method actually parsing the replay and saving the result in a property
	 *
	 * @access public
	 */
	public function parse() {
		$cachePath = sys_get_temp_dir().DIRECTORY_SEPARATOR."sc2rep_cache".DIRECTORY_SEPARATOR."{$this->identify["repHash"]}".DIRECTORY_SEPARATOR;

		$json = exec(co\registry('pythonExe').' '.co\registry('app.path')."/lib/parse.py {$cachePath} {$this->path} 2>&1", $out, $retcode);
		if($retcode != 0) {
			throw new ParseException("the replay file appears to be corrupt [2]", 400);
		}

		$this->data = json_decode($json, true);
	}

	/**
	 * method attempting to import a replay file from an array that comes from the python parser
	 *
	 * @access public
	 * @return integer id of the processed match (will throw exception in error cases)
	 */
	public function import() {
		ar\Database::init()->transaction();

		$ret = new models\MatchModel([
			"identifier" => $this->data["repHash"],
			"isLadder" => $this->data["ladder"],
			"played" => date("Y-m-d H:i:s", $this->data["timestamp"]),
			"length" => $this->data["length"],
			"loops" => $this->data["loops"],
			"idStatus" => 1//newly imported
		]);

		if(!$ret->save()) {
			throw new ParseException("error saving the match details [1]", 400);
		}

		if($this->data["ladder"]) {
			//try to get the rank for the match at that time for a full team
			foreach ($this->data["teams"] as $team) {
				$ranking = RankedFTWMiner::getRanking($team["fullRanked"], $this->data["timestamp"]);
				if($ranking !== false) {
					foreach ($team["players"] as $sid) {
						$this->data["players"][$sid]["ranking"] = $ranking;
					}
				}
			}

		}

		foreach($this->data["players"] as $pl) {
			$player = models\PlayerModel::get(["bnet" => $pl["bnetId"]]);
			if($player === null) {
				$player = models\PlayerModel::create([
					"name" => $pl["name"],
					"clantag" => $pl["clantag"],
					"url" => $pl["url"],
					"bnet" => $pl["bnetId"]
				]);

				if($player === false) {
					throw new ParseException("error saving the match details [2]", 400);
				}
			} else {
				//save updated bnet information
				$player->name = $pl["name"];
				$player->clantag = $pl["clantag"];
				$player->url = $pl["url"];
			}

			//mine data from bnet profile
			$player->mineBnetProfile();

			$perf = new models\PerformanceModel([
				"player" => $player,
				"team" => $pl["teamId"],
				"pickRace" => models\TagModel::get(["name" => $pl["pickedRace"]])->id,
				"playRace" => models\TagModel::get(["name" => $pl["race"]])->id,
				"sid" => $pl["sid"],
				"idMatch" => $ret->id,
				"isWin" => $pl["win"]
			]);
			//save his performance details
			$perf->update($pl);

			if($pl["bnetId"] != 0 && $this->data["ladder"]) {
				//check if the team ranking set the ranking for the player already (1v1 and archon was handled by the team search, since there is no 1v1-random)
				if(!isset($pl["ranking"]) && !in_array($this->data["tags"]["gametype"], ["1v1", "archon"])) {
					//try to find a random team ranking
					$getParams = "?mode=random-{$this->data["tags"]["gametype"]}&player={$pl["url"]}";
					$ranking = RankedFTWMiner::getRanking($getParams, $this->data["timestamp"]);
					if($ranking !== false) {
						$pl["ranking"] = $ranking;
					}
				}

				if(isset($pl["ranking"])) {
					//one of the two methods has found a ranking => save into performance
					$tags[$pl["ranking"]["league"]] = models\TagModel::findOrCreateTag(ucfirst($pl["ranking"]["league"]), "league");
					$perf->league = $tags[$pl["ranking"]["league"]]->id;

					unset($pl["ranking"]["league"]); //allow the rest of the data to be set with a loop
					foreach ($pl["ranking"] as $key => $val) {
						$perf->__set($key, $val);
					}
				}

			}

			$player->save();

			if(!$perf->save()) {
				throw new error\HFWException("Could not save player peformance for {$pl["name"]} valid?: ".var_export($perf->isValid(), true), 400);
			}
		}

		if($this->data["ladder"]) {
			foreach (models\SeasonModel::all() as $season) {
				if($season->end === null) {
					if($this->data["timestamp"] > strtotime($season->start)) {
						$tags[] = $season->tag;
					}
				} elseif($this->data["timestamp"] > strtotime($season->start) && $this->data["timestamp"] < strtotime($season->end)) {
					$tags[] = $season->tag;
					break;
				}
			}
		}

		foreach ($this->data["tags"] as $group => $tag) {
			$tags[] = models\TagModel::findOrCreateTag($tag, $group);
		}

		$ret->tags = $tags;
		$ret->idStatus = 3;

		if($ret->save()) {
			move_uploaded_file($this->path, $ret->getPath());
			ar\Database::init()->commit();
			return $ret->id;
		} else {
			ar\Database::init()->rollback();
			throw new error\HFWException("Could not update imported match with performances/tags valid?: ".var_export($ret->isValid(), true));
		}
	}

	/**
	 * method attempting to import a replay file from an array that comes from the python parser
	 *
	 * @access public
	 * @param  models\MatchModel match | the match to be updated
	 * @return integer id of the processed match (will throw exception in error cases)
	 */
	public function update(models\MatchModel $match) {
		ar\Database::init()->transaction();

		foreach ($match->performances as $perf) {
			$pl = $this->data["players"][$perf->sid];
			$perf->isWin = $pl["win"] ? "1" : "0";
			//save his performance details
			$perf->update($pl);

			if(!$perf->save()) {
				throw new error\HFWException("Could not update player peformance for ".var_export($pl, true)." valid?: ".var_export($perf->isValid(), true));
			}
		}

		move_uploaded_file($this->path, $match->getPath());
		$match->idStatus = 3;
		$match->save();
		ar\Database::init()->commit();
		return $match->id;
	}

}