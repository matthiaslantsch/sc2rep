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
 * create the performance table
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class PerformanceCreateMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('performance', function($t) {
			$t->integer("RCR")->nullable();
			$t->integer("SQ")->nullable();
			$t->integer("AU")->nullable();
			$t->integer("APM")->nullable();
			$t->boolean("isWin")->nullable();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("performance");
	}

}