<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * Model class for the PerformanceModel model class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\models;

/**
 * PerformanceModel to wrap around the performance table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\models
 */
class PerformanceModel extends TagModel {

	/**
	 * contains relationship mapping for belongsTo
	 *
	 * @access public
	 * @var    array $belongsTo Array with definitions for a belongs to relationship
	 */
	public static $belongsTo = ["match", "player"];

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var    array $validate Array with verification data
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
	 *
	 * @access public
	 * @param  array $newData Array with keys as described above
	 * @return void
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
