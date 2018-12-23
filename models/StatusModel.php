<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * Model class for the StatusModel model class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\models;

use holonet\activerecord\ModelBase;

/**
 * StatusModel to wrap around the status table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\models
 */
class StatusModel extends ModelBase {

	/**
	 * contains relationship mapping for hasMany
	 *
	 * @access public
	 * @var    array $hasMany Array with definitions for a has many relationship
	 */
	public static $hasMany = array("matches");

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var    array $validate Array with verification data
	 */
	public static $validate = array(
		"name" => ["presence"]
	);

}
