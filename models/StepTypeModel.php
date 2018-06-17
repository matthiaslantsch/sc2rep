<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the StepType model
 */

namespace HIS5\sc2rep\models;

use HIS5\lib\activerecord as ar;

/**
 * stepType model class
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\models
 */
class StepTypeModel extends ar\ModelBase {

	/**
	 * property containing hasMany relationship mappings
	 *
	 * @access 	public
	 * @var 	array with relationships
	 */
	public static $hasMany = ["step"];

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access 	public
	 * @var 	array with verification data
	 */
	public static $validate = array(
		"name" => ["presence", "length" => ["max" => 10]]
	);

}
