<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the performance table
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the performance table
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\db\migrate
 */
class PerformanceCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createTable('performance', function($t) {
			$t->integer("RCR")->nullable();
			$t->integer("SQ")->nullable();
			$t->integer("AU")->nullable();
			$t->integer("APM")->nullable();
			$t->boolean("isWin")->nullable();
			$t->integer("team");
			$t->integer("secondBase")->nullable();
			$t->integer("thirdBase")->nullable();
			$t->integer("onebaseSat")->nullable();
			$t->integer("twobaseSat")->nullable();
			$t->integer("threebaseSat")->nullable();
			$t->integer("workerCount")->nullable();
			$t->integer("sid");
			$t->string("divisionRank")->size(20)->nullable();
			$t->string("leagueRank")->size(20)->nullable();
			$t->string("serverRank")->size(20)->nullable();
			$t->string("globalRank")->size(20)->nullable();
			$t->integer("points")->nullable();
			$t->integer("winrate")->nullable();
			$t->addReference("match");
			$t->addReference("player");
			$t->addReference("race", "pickRace", "idTag");
			$t->addReference("race", "playRace", "idTag");
			$t->addReference("tag", "league", "idTag")->nullable();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("performance");
	}

}
