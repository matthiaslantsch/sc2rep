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
 * alter the build table
 * add author shortDesc longDesc 
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class BuildAddAuthorShortDescLongDescMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::changeTable("build", function($t) {
			$t->addColumn("idUser")->integer()->references("user");
			$t->addColumn("descBrief")->string("255")->nullable();
			$t->addColumn("descLonger")->text()->nullable();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::changeTable("build", function($t) {
			$t->dropReference("user");
			$t->dropColumn("descBrief");
			$t->dropColumn("descLonger");
		});
	}

}