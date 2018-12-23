<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the map table
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the map table (extending the "tag" table)
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\db\migrate
 */
class MapCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createExtendsTable('map', 'tag', function($t) {
			$t->string("identifier")->size('255');
			$t->integer("sizeX")->nullable();
			$t->integer("sizeY")->nullable();
			$t->boolean("denied");
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("map");
	}

}
