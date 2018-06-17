<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Build model
 */

namespace HIS5\sc2rep\models;

/**
 * build model class
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\models
 */
class BuildModel extends TagModel {

	/**
	 * property containing belongsTo relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $belongsTo = ["buildType"];

	/**
	 * property containing extends relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $extends = ["tag"];

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $hasMany = ["steps"];
	
	/**
	 * property containing verification data for some of the columns
	 *
	 * @access 	public
	 * @var 	array with verification data
	 */
	public static $validate = array(
		"name" => ["presence", "length" => ["max" => 255]],
		"descBrief" => ["length" => ["max" => 255]]
	);

	/**
	 * getter method to get a string for the author
	 * if there is no author, we'll claim the credit :)
	 *
	 * @access public
	 * @return string username of the author/sc2rep tool if no author
	 */
	public function getAuthor() {
		if(!isset($this->idUser)) {
			return "sc2rep tool";
		} else {
			UserModel::find($this->idUser)->name;
		}
	}

	/**
	 * static method allowing the encoding of a build from a php array (e.g. from the python script)
	 *
	 * @access public
	 * @param  array with the steps ["type("army/worker/struct/upgrade/morph"), "name", "foodUsed", "foodMade", "started", "spawned", "died", "location"]
	 * @return BuildModel object of the build
	 */
	public static function fromArray($steps, $name = "The build") {
		$typeIds = ["army" => 1, "worker" => 2, "struct" => 3, "upgrade" => 4, "morph-army" => 1, "morph-struct" => 3];
		$me = new static(["name" => $name]);
		usort($steps, function($a, $b) {
			return $a["started"] - $b["started"];
		});

		$stepsToSave = [];
		foreach (array_values($steps) as $order => $step) {
			if($step["spawned"] > 0) {
				$stepsToSave[$order] = new StepModel([
					"asset" => $step["name"],
					"supply" => $step["foodUsed"],
					"time" => $step["started"],
					"order" => $order,
					"type" => $typeIds[$step["type"]],
					"chrono" => false //for now
				]);

				if($stepsToSave[$order]->type == 3 && isset($step["location"])) {
					//it's a structure, save the location
					$stepsToSave[$order]->location = $step["location"];
				}
			}
		}
		$me->step = $stepsToSave;

		return $me;
	}

	/**
	 * magic method calling the different encoders
	 * used with e.g. toSALT => SALTEncoder::encode()
	 *
	 * @access public
	 * @param  string name  | the name of the called method
	 * @param  array arguments | additional arguments for the encoder
	 * @return mixed answer | the answer of the called encoder, or else null
	 */
	public function __call($name, $arguments) {
		if(strpos($name, "to") === 0) {
			$className = "\\HIS5\\sc2rep\\helpers\\buildencoder\\".str_replace("to", "", $name)."Encoder";
			return $className::encode($this);
		}
		return parent::__call($name, $arguments);
	}

	/**
	 * small getter function returning empty description if not given any
	 *
	 * @access public
	 * @return string descBrief | brief description of the build
	 */
	public function getDescBrief() {
		if(!isset($this->descBrief)) {
			return "";
		} else {
			return $this->descBrief;
		}
	}

}