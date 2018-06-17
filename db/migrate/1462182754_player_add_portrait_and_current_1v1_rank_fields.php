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
 * add portrait and current 1v1 rank fields 
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class PlayerAddPortraitAndCurrent1v1RankFieldsMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::changeTable("player", function($t) {
			$t->addColumn("portrait")->string()->nullable();
			$t->addColumn("curLeague")->string(10)->nullable();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::changeTable("player", function($t) {
			$t->dropColumn("portrait");
			$t->dropColumn("curLeague");
		});
	}

}