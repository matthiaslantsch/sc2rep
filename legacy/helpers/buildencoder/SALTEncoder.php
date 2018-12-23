<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the SALT build encoder class
 */

namespace HIS5\sc2rep\helpers\buildencoder;

use HIS5\sc2rep\models as models;

/**
 * encoder class for the SALT build tool format
 * format documentation: https://www.reddit.com/r/starcraft/comments/25losh/build_order_tool_salt_update/
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\helpers\buildencoder
 */
class SALTEncoder implements Encoder {

	/**
	 * static method encoding a Build contained in a BuildModel object into a SALT string
	 *
	 * @access public
	 * @param  BuildModel object | the BuildModel object containing the build
	 * @return SALT string | a SALT build order string
	 */
	public static function encode(models\BuildModel $build) {
		$ret = "&"; //salt format version ?
		//build name (cannot contain ~ as that is the end character for the title in the format)
		$ret .= str_replace("~", "-", $build->name)."|{$build->author}|{$build->descBrief}~";
		//in order to skip every entry that repeats more than 10 times, we need to create a array
		$repeat = [];
		foreach ($build->step as $step) {
			if($step->type == 2) {
				//worker: skip
				continue;
			}

			if(!isset($repeat[$step->asset])) {
				$repeat[$step->asset] = 0;
			} else {
				//do not include more than 3 entries of supply units
				if(in_array($step->asset, ["pylon", "supplydepot", "overlord"]) && $repeat[$step->asset] >= 3) {
					continue;
				} elseif($repeat[$step->asset] >= 10) {
					//do not include more than 10 entries of the same unit
					continue;
				}
			}

			//calculate the minutes and seconds from the time in the step
			if($step->time >= 60) {
				if($step->time % 60 == 0) {
					$seconds = 0;
					$minutes = $step->time / 60;
				} else {
					$minutes = (int)($step->time / 60);
					$seconds = (int)($step->time % 60);
				}
			} else {
				$seconds = $step->time;
				$minutes = 0;

			}

			//format per step: "Supply,Minutes,Seconds,Location (x),Location (y),Asset,Description (brief),Description (longer);"
			$ret .= sprintf("%s,%s,%s,%s,%s,%s,%s,%s;",
				$step->supply,
				$minutes,
				$seconds,
				($step->location === null ? "" : $step->location["x"]),
				($step->location === null ? "" : $step->location["y"]),
				$step->asset,
				$step->descBrief,
				$step->descLonger
			);
		}

		return $ret;
	}

	/**
	 * static method decoding a Build contained in a SALT string into a BuildModel object
	 *
	 * @access public
	 * @param  saltStr String | the SALT string to decode
	 * @return BuildModel object | the BuildModel object containing the build
	 */
	public static function decode($saltStr) {
		die("not yet implemented");
	}

}