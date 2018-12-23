<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * procedural entry point file for web requests using the development php cli server
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

use holonet\common as co;

$rootdir = dirname(__DIR__);
require_once implode(DIRECTORY_SEPARATOR, array($rootdir, "vendor", "autoload.php"));

//check which app we're working with
$url = ltrim($_SERVER["REQUEST_URI"], "/");
//make sure the url ends with a trailing slash
if(!empty($url) && $url[-1] !== "/") {
	$url .= "/";
}
$project = strstr($url, "/", true);
if($project === false || !is_dir("{$_SERVER["DOCUMENT_ROOT"]}/{$project}")) {
	$project = "sc2rep";
	$_SERVER["PATH_INFO"] = $_SERVER["REQUEST_URI"];
	$_SERVER["PHP_SELF"] = "entry.php";
} else {
	$_SERVER["PHP_SELF"] = "{$project}/entry.php";
	$_SERVER["PATH_INFO"] = str_replace($project, "", $_SERVER["REQUEST_URI"]);
}

//turn on debug for dev
co\registry("debug", true);
//return files from the public directory
if (is_file($_SERVER["DOCUMENT_ROOT"].$_SERVER["REQUEST_URI"])) {
	return false;
} else {
	$app = new holonet\holofw\FWApplication($rootdir, $project);
	$request = holonet\http\HttpRequest::createFromGlobals();
	$app->handle($request)->send();
}
