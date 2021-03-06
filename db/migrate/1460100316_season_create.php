<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the season table
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the season table (extending the "tag" table)
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\db\migrate
 */
class SeasonCreateMigration implements Migration {

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
