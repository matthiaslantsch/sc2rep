<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the custom recoverable exception
 */

namespace HIS5\sc2rep\helpers;

/**
 * The ParseException class is used to recoverable exceptions during replay parsing
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\helpers
 */
class ParseException extends \Exception {
	
	/**
	 * constructor method for the exception
	 *
	 * @access public
	 * @param  string msg | Error message
	 * @param  int errorcode | Error code
	 */
	public function __construct($msg, $errorcode) {
		parent::__construct($msg, $errorcode);
	}

}