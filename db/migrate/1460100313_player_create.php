<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the player table
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the player table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\db\migrate
 */
class PlayerCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('player', function($t) {
			$t->string("name")->size(45);
			$t->string("clantag")->size(10);
			$t->string("url");
			$t->string("bnet")->size(10);
			$t->string("portrait")->nullable();
			$t->string("curLeague")->size(15)->nullable();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("player");
	}

}
