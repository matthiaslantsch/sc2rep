<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * Model class for the TagModel model class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\models;

use holonet\activerecord\ModelBase;

/**
 * TagModel to wrap around the tag table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\models
 */
class TagModel extends ModelBase {

	/**
	 * contains relationship mapping for hasOne
	 *
	 * @access public
	 * @var    array $hasOne Array with definitions for a has one relationship
	 */
	public static $hasOne = array(
		"race" => array("forced" => false),
		"season" => array("forced" => false),
		"map" => array("forced" => false)
	);

	/**
	 * property containing many2many relationship mappings
	 *
	 * @access public
	 * @var    array $many2many Array with relationship mappings
	 */
	public static $many2many = array("matches");

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var    array $validate Array with verification data
	 */
	public static $validate = array(
		"name" => array("presence", "length" => array("max" => 255)),
		"group" => array("presence", "length" => array("max" => 255))
	);

	/**
	 * helper method either creating a new tag or returning the existing one
	 *
	 * @access public
	 * @param  string $name of the tag
	 * @param  string $name of the tag group (optional)
	 * @return instance of this class either newly created or existing
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
		], true);
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
