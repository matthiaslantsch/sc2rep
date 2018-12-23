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
 * rename saturation and basetiming columns, remove 1 base saturation speed 
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class PerformanceRenameSaturationAndBasetimingColumnsRemove1BaseSaturationSpeedMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::changeTable("performance", function($t) {
			$t->changeColumn("2ndBase")->rename("secondBase");
			$t->changeColumn("3rdBase")->rename("thirdBase");
			$t->changeColumn("1baseSat")->rename("onebaseSat");
			$t->changeColumn("2baseSat")->rename("twobaseSat");
			$t->changeColumn("3baseSat")->rename("threebaseSat");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::changeTable("performance", function($t) {
			$t->changeColumn("secondBase")->rename("2ndBase");
			$t->changeColumn("thirdBase")->rename("3rdBase");
			$t->changeColumn("onebaseSat")->rename("1baseSat");
			$t->changeColumn("twobaseSat")->rename("2baseSat");
			$t->changeColumn("threebaseSat")->rename("3baseSat");
		});
	}

}