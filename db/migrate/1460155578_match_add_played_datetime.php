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
 * alter the match table
 * add played datetime
 *  
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class MatchAddPlayedDatetimeMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::changeTable("match", function($t) {
			$t->addColumn("played")->timestamp();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::changeTable("match", function($t) {
			$t->dropColumn("played");
		});
	}

}