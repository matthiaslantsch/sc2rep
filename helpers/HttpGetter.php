<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the HttpGetter class
 */

namespace HIS5\sc2rep\helpers;

use HIS5\lib\Common as co;

/**
 * abstraction class to isolate the stream_context logic
 * offers the stream_context request logic to all miner classes
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\helpers
 */
class HttpGetter {

	/**
	 * method executing a http request to the given url
	 * uses registry("httpProxy") for the request
	 *
	 * @access public
	 * @param  string url | the url for the request
	 * @param  string expected code | a response code that the header should be checked with (use null for no checking)
	 * @return string answer | the http answer for the executed request
	 */
	public static function request($url, $expectedCode = "200") {
		$aContext = array(
			'http' => array(
				'request_fulluri' => true,
				'ignore_errors' => true
			),
		);

		if(($proxy = co\registry("httpProxy")) !== false) {
			$aContext["http"]["proxy"] = $proxy;
		}

		$cxContext = stream_context_create($aContext);

		$response = @file_get_contents($url, false, $cxContext);

		//check the response code
		if($expectedCode !== null && (!isset($http_response_header[0]) || strpos($http_response_header[0], $expectedCode) === false)) {
			return false;
		}
		
		return $response;
	}

}