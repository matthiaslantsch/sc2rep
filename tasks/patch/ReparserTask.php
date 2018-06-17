<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for migration reparser task
 */

namespace HIS5\sc2rep\tasks\patch;

use HIS5\holoFW\core\baseclasses as base;
use HIS5\holoFW\tasks\db as fwDbTasks;
use HIS5\lib\Common as co;
use HIS5\sc2rep\helpers as helpers;

/**
 * reparser task executes a db/schema::setup and then reimports all the current replay files in updir/
 * 
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\tasks\patch
 */
class ReparserTask extends base\TaskBase {

	/**
	 * constructor helper function reseting the database
	 *
	 * @access public
	 */
	public function __construct() {
		echo "Resetting up database...\n";
		fwDbTasks\SchemaTask::setup();
	}

	/**
	 * mandatory desc function describing the task to stdout
	 *
	 * @access public
	 * @return desc a string text describing the task
	 */
	public function desc() {
		return <<<DESC
reparser task executes a db/schema::setup and then reimports all the current replay files in updir/
Avaible modes:
	::reparse resetups the database and reimports all the current replays
DESC;
	}

	/**
	 * mode reparse reparses all the replay currently in the updir
	 *
	 * @access public
	 */
	public function reparse() {
		echo "Reparsing replay files...\n";
		$tempDir = sys_get_temp_dir().DIRECTORY_SEPARATOR."sc2rep_migrate";
		if(!file_exists($tempDir)) {
			mkdir($tempDir);
		}
		foreach (glob(co\registry("app.path").DIRECTORY_SEPARATOR."updir".DIRECTORY_SEPARATOR."*.SC2replay") as $rep) {
			rename($rep, $tempDir.DIRECTORY_SEPARATOR.basename($rep));
		}

		foreach (glob(sys_get_temp_dir().DIRECTORY_SEPARATOR."sc2rep_migrate".DIRECTORY_SEPARATOR."*.SC2replay") as $rep) {
			echo "Processing {$rep}...";
			try {
				$importer = new helpers\ImportHelper($rep);
				echo " => id {$importer->process()}\n";
			} catch (\Exception $e) {
				echo " => Error: \n{$e->getMessage()}\n";
			}
		}
	}

}