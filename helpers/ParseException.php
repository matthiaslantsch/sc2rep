<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the custom recoverable exception
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\helpers;

/**
 * The ParseException class is used to recoverable exceptions during replay parsing
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\helpers
 */
class ParseException extends \RuntimeException {

	/**
	 * constructor method for the exception
	 *
	 * @access public
	 * @param  string $msg Error message
	 * @param  int $errorcode Error code
	 * @return void
	 */
	public function __construct($msg, $errorcode) {
		parent::__construct($msg, $errorcode);
	}

}
