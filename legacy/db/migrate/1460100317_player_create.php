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
 * create the player table
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class PlayerCreateMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('player', function($t) {
			$t->string("name")->setSizeDef('45');
			$t->string("clantag")->setSizeDef('10');
			$t->string("url")->setSizeDef('255');
			$t->integer("idUser")->nullable();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("player");
	}

}