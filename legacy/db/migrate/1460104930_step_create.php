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
 * create the step table
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class StepCreateMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('step', function($t) {
			$t->integer("supply");
			$t->integer("time");
			$t->integer("order");
			$t->string("asset")->setSizeDef("40");
			$t->string("descBrief")->setSizeDef("40")->nullable();
			$t->string("descLonger")->setSizeDef("255")->nullable();
			$t->boolean("chrono");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("step");
	}

}