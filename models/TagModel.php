<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Tag model
 */

namespace HIS5\sc2rep\models;

use HIS5\lib\activerecord as ar;

/**
 * tag model class
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\models
 */
class TagModel extends ar\ModelBase {

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $hasOne = [
		"build" => ["forced" => false],
		"race" => ["forced" => false],
		"season" => ["forced" => false],
		"map" => ["forced" => false]
	];

	/**
	 * property containing belongsTo relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $many2many = ["matches"];
	
	/**
	 * property containing verification data for some of the columns
	 *
	 * @access 	public
	 * @var 	array with verification data
	 */
	public static $validate = array(
		"name" => ["presence", "length" => ["max" => 255]],
		"group" => ["presence", "length" => ["max" => 40]]
	);

	/**
	 * helper method either creating a new tag or returning the existing one
	 *
	 * @access public
	 * @param  string name of the tag
	 * @param  string name of the tag group (optional)
	 */
	public static function findOrCreateTag($name, $group = "") {
		$opts = ["name" => $name];
		if($group !== "") {
			$opts["group"] = $group;
		}

		if(($tag = static::get($opts)) !== null) {
			return $tag;
		}

		//not found; create it
		return static::create([
			"name" => $name,
			"group" => ($group !== "" ? $group : "other")
		]);
	}

	/**
	 * magic method turning the tag into a string, effectively just returning the name of the tag
	 *
	 * @access public
	 * @return name of this tag
	 */
	public function __toString() {
		return $this->name;
	}

}