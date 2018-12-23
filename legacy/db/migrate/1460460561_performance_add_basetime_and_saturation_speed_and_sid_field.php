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
 * alter the performance table
 * add basetime and saturation speed and sid field
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class PerformanceAddBasetimeAndSaturationSpeedAndSidFieldMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::changeTable("performance", function($t) {
			$t->addColumn("2ndBase")->integer()->nullable();
			$t->addColumn("3rdBase")->integer()->nullable();
			$t->addColumn("1baseSat")->integer()->nullable();
			$t->addColumn("2baseSat")->integer()->nullable();
			$t->addColumn("3baseSat")->integer()->nullable();
			$t->addColumn("workerCount")->integer()->nullable();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::changeTable("performance", function($t) {
			$t->dropColumn("2ndBase");
			$t->dropColumn("3rdBase");
			$t->dropColumn("1baseSat");
			$t->dropColumn("2baseSat");
			$t->dropColumn("3baseSat");
			$t->dropColumn("workerCount");
		});
	}

}