<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * migration class file
 */

namespace HIS5\sc2rep\db\migrate;

use HIS5\lib\activerecord as activerecord;
use HIS5\lib\activerecord\Schema as Schema;

/**
 * alter the player table
 * add bnet id field 
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class PlayerAddBnetIdFieldMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::changeTable("player", function($t) {
			$t->addColumn("bnet")->string(10);
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::changeTable("player", function($t) {
			$t->dropColumn("bnet");
		});
	}

}