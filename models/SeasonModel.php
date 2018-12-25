<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * Model class for the SeasonModel model class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\models;

/**
 * SeasonModel to wrap around the season table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\models
 */
class SeasonModel extends TagModel {

	/**
	 * property containing extends relationship mappings
	 *
	 * @access public
	 * @var    array $extends Array with relationship definitions
	 */
	public static $extends = ["tag"];

	/**
	 * property containing verification data for some of the columns
	 *
	 * @access public
	 * @var    array $validate Array with verification data
	 */
	public static $validate = array(
		"start" => ["presence"],
		"number" => ["presence"]
	);

	/**
	 * convenience alias function for select([])
	 * overwritten to sort the seasons from newest to oldest by default
	 *
	 * @access public
	 * @return array with the result objects
	 */
	public static function all() {
		return static::select(array("ORDER" => "start"));
	}
}
