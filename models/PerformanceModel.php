<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Performance model
 */

namespace HIS5\sc2rep\models;

use HIS5\lib\activerecord as ar;

/**
 * performance model class
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\models
 */
class PerformanceModel extends ar\ModelBase {

	/**
	 * property containing belongsTo relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $belongsTo = ["match", "player"];

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access 	public
	 * @var 	array with verification data
	 */
	public static $validate = array(
		"pickRace" => ["presence"],
		"playRace" => ["presence"],
		"sid" => ["presence"],
		"team" => ["presence"]
	);

	/**
	 * getter method to get the picked race
	 *
	 * @access public
	 * @return string the picked race
	 */
	public function getPickedRace() {
		return TagModel::find($this->data["pickRace"])->name;
	}

	/**
	 * getter method to get the played race
	 *
	 * @access public
	 * @return string the played race
	 */
	public function getPlayedRace() {
		return TagModel::find($this->data["playRace"])->name;
	}

	/**
	 * getter method to get the current league at the time the match was played
	 *
	 * @access public
	 * @return string the league at the time the match
	 */
	public function getCurrentLeague() {
		$tag = TagModel::find($this->data["league"]);
		if($tag !== null) {
			return $tag->name;
		} else {
			return "default";
		}
	}

	/**
	 * update method used to save performance details in the performance data
	 * the array can update with these keys:
	 * 	basetimings[secondBase, thirdBase]
	 * 	saturationTimings[onebaseSat, twobaseSat, threebaseSat]
	 * 	averageUnspent
	 * 	averageIncome
	 * 	spendingSkill
	 * @access public
	 * @param  array data | array with keys as described above
	 */
	public function update($newData) {
		if(isset($newData["basetimings"][0])) {
			//2nd base timing
			$this->__set("secondBase", ceil($newData["basetimings"][0]));
		}

		if(isset($newData["basetimings"][1])) {
			//3rd base timing
			$this->__set("thirdBase", ceil($newData["basetimings"][1]));
		}

		if(isset($newData["saturationTimings"][0])) {
			//1 base saturation timing
			$this->__set("onebaseSat", ceil($newData["saturationTimings"][0]));
		}

		if(isset($newData["saturationTimings"][1])) {
			//2 base saturation timing
			$this->__set("twobaseSat", ceil($newData["saturationTimings"][1]));
		}

		if(isset($newData["saturationTimings"][2])) {
			//3 base saturation timing
			$this->__set("threebaseSat", ceil($newData["saturationTimings"][2]));
		}

		if($newData["averageUnspent"] > 0) {
			$this->AU = $newData["averageUnspent"];
		}

		if($newData["averageIncome"] > 0) {
			$this->RCR = $newData["averageIncome"];
		}

		if($newData["averageIncome"] > 0 && $newData["averageUnspent"] > 0) {
			//probably not an older replay
			$this->SQ = $newData["spendingSkill"];
		}

		if($newData["workersBuilt"] > 0) {
			$this->workerCount = $newData["workersBuilt"];
		}

		if($newData["averageApm"] > 0) {
			$this->APM = $newData["averageApm"];
		}
	}

}
