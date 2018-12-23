<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * Model class for the RaceModel model class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\models;

/**
 * RaceModel to wrap around the race table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\models
 */
class RaceModel extends TagModel {

	/**
	 * property containing extends relationship mappings
	 *
	 * @access public
	 * @var    array $extends Array with relationship definitions
	 */
	public static $extends = array("tag");

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var    array $validate Array with verification data
	 */
	public static $validate = array (
		"isPlayable" => ["presence"]
	);

}
