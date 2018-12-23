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
 * add current ranking columns 
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\db\migrate
 */
class PerformanceAddCurrentRankingColumnsMigration implements activerecord\Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::changeTable("performance", function($t) {
			$t->addColumn("divisionRank")->string("20")->nullable();
			$t->addColumn("leagueRank")->string("20")->nullable();
			$t->addColumn("serverRank")->string("20")->nullable();
			$t->addColumn("globalRank")->string("20")->nullable();
			$t->addColumn("league")->integer()->nullable()->references("tag", "idTag");
			$t->addColumn("points")->integer()->nullable();
			$t->addColumn("winrate")->integer()->nullable();
		});
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::changeTable("performance", function($t) {
			$t->dropColumn("divisionRank");
			$t->dropColumn("leagueRank");
			$t->dropColumn("serverRank");
			$t->dropColumn("globalRank");
			$t->dropColumn("league");
			$t->dropColumn("points");
			$t->dropColumn("winrate");
		});
	}

}