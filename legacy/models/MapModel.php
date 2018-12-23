<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the Map model
 */

namespace HIS5\sc2rep\models;

use HIS5\lib\activerecord as ar;
use HIS5\lib\Common as co;
use HIS5\sc2rep\helpers as helpers;

/**
 * map model class
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\models
 */
class MapModel extends TagModel {

	/**
	 * property containing extends relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $extends = ["tag"];

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access 	public
	 * @var 	array with verification data
	 */
	public static $validate = array(
		"identifier" => ["presence", "length" => ["max" => 255]],
		"denied" => ["presence"]
	);

	/**
	 * getter returns the standardized minimap path
	 *  public/gfx/maps/{$this->identifier}.jpg
	 *
	 * @access public
	 * @return string path | the path of the minimap image
	 */
	public function minimapPath() {
		return co\registry("app.path").DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR.
			"gfx".DIRECTORY_SEPARATOR."maps".DIRECTORY_SEPARATOR."{$this->identifier}.jpg";
	}

}
