<?php
/**
 * This file is part of the sc2rep replay parser tool
 * (c) Matthias Lantsch
 *
 * file for common viewhelper functions
 */

use HIS5\lib\Common as co;

/**
 * small helper function creating a timing string out of a second number
 *
 * @access public
 * @return string the given seconds time as a string
 */
function transformToTimestring($seconds) {
	if($seconds === null) {
		return '-';
	}

	if($seconds >= 60) {
		if($seconds % 60 == 0) {
			return twoDigitNumber($seconds / 60).' : 00';
		}

		return twoDigitNumber((int)($seconds / 60)).' : '.twoDigitNumber((int)($seconds % 60));
	} else {
		return '00 : '.twoDigitNumber($seconds);
	}
}

/**
 * small helper function making sure the number always has two digits
 * e.g. 1 => 01, 10 => 10, 4 => 04
 *
 * @access public
 * @param  number the number to format
 * @return string a two digit number
 */
function twoDigitNumber($number) {
	if($number < 10) {
		return '0'.$number;
	} else {
		return $number;
	}
}

/**
 * small helper function to construct a string like 2 min 15 s
 *
 * @access public
 * @param  the time in seconds
 * @return the time string
 */
function constructTimeString($time) {
	if($time >= 86400) {
		if($time % 86400 == 0) {
			return $time / 86400 . "days";
		}
		return (int)($time / 86400) . 'days ' . (int)($time % 86400 / 3600) . 'h';
	} elseif($time >= 3600) {
		if($time % 3600 == 0) {
			return $time / 3600 . "h";
		}
		return (int)($time / 3600) . 'h ' . (int)($time % 3600 / 60) . 'min';
	} if($time >= 60) {
		if($time % 60 == 0) {
			return $time / 60 . "min";
		}
		return (int)($time / 60) . 'min ' . (int)($time % 60) . 's';
	} else {
		return $time . 's';
	}
}