<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * class file for a migration to create the match2tag table
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\db\migrate;

use holonet\activerecord\Migration;
use holonet\activerecord\Schema;

/**
 * create the match2tag table (resolving between the match and the tag tables)
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\db\migrate
 */
class Match2tagCreateMigration implements Migration {

	/**
	 * migration into the up direction
	 */
	public static function up() {
		Schema::createResolutionTable('match', 'tag', '1460100318');
	}

	/**
	 * migration into the down direction
	 */
	public static function down() {
		Schema::dropTable("match2tag");
	}

}
