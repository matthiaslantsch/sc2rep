<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the tag table
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the tag table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\db\migrate
 */
class TagCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('tag', function($t) {
			$t->string("name");
			$t->string("group")->size('40');
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("tag");
	}

}
