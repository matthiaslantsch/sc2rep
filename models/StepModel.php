<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Step model
 */

namespace HIS5\sc2rep\models;

use HIS5\lib\activerecord as ar;

/**
 * step model class
 * represents a step in a build order
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\models
 */
class StepModel extends ar\ModelBase {

	/**
	 * property containing belongsTo relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $belongsTo = ["build", "stepType"];
	
	/**
	 * property containing verification data for some of the columns
	 *
	 * @access 	public
	 * @var 	array with verification data
	 */
	public static $validate = array(
		"supply" => ["presence"],
		"time" => ["presence"],
		"order" => ["presence"],
		"asset" => ["presence", "length" => ["max" => 40]],
		"descBrief" => ["length" => ["max" => 40]],
		"descLonger" => ["length" => ["max" => 255]],
		"chrono" => ["presence"]
	);

	/**
	 * property containing location data about where the structure should be placed
	 *
	 * @access 	public
	 * @var 	array with x and y coordinates
	 */
	public $location;

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

	/**
	 * small getter function returning empty description if not given any
	 *
	 * @access public
	 * @return string descLonger | brief description of the build
	 */
	public function getDescLonger() {
		if(!isset($this->descLonger)) {
			return "";
		} else {
			return $this->descLonger;
		}
	}

}
