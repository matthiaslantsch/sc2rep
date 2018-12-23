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
 * create the season table
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class SeasonCreateMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createExtendsTable('season', 'tag', function($t) {
			$t->datetime("start");
			$t->datetime("lock")->nullable();
			$t->datetime("end")->nullable();
			$t->integer("number");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("season");
	}

}