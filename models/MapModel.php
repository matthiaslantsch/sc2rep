<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * Model class for the MapModel model class
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\models;

use holonet\common as co;

/**
 * MapModel to wrap around the map table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\models
 */
class MapModel extends TagModel {

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
		"identifier" => ["presence", "length" => ["max" => 255]],
		"denied" => ["presence"]
	);

	/**
	 * getter returns the standardized minimap path
	 *  public/gfx/maps/{$this->identifier}.jpg
	 *
	 * @access public
	 * @return string with the path to the minimap file
	 */
	public function minimapPath() {
		return co\filepath(
			co\registry("app.publicdir"), "sc2rep", "gfx", "maps", "{$this->identifier}.jpg"
		);
	}

}
