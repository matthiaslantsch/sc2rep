<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the match table
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the match table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\db\migrate
 */
class MatchCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('match', function($t) {
			$t->string("identifier");
			$t->boolean("isLadder");
			$t->integer("loops");
			$t->timestamp("played");
			$t->integer("length");
			$t->addReference("status");
			$t->addReference("match", "idMatch", "idMatch");
			$t->addReference("player", "idPlayer", "idPlayer");
			$t->addReference("race", "pickRace", "idTag");
			$t->addReference("race", "playRace", "idTag");
			$t->addReference("tag", "league", "idTag")->nullable();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("match");
	}

}
