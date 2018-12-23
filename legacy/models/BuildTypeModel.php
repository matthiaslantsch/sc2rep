<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the BuildType model
 */

namespace HIS5\sc2rep\models;

use HIS5\lib\activerecord as ar;

/**
 * buildType model class
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\models
 */
class BuildTypeModel extends ar\ModelBase {

	/**
	 * property containing relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $hasMany = ["builds"];
	
	/**
	 * property containing verification data for some of the columns
	 *
	 * @access 	public
	 * @var 	array with verification data
	 */
	public static $validate = array(
		"name" => ["presence", "length" => ["max" => 40]]
	);

}