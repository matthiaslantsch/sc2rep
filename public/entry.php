<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * procedural entry point file for web requests
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

$rootdir = dirname(__DIR__);
require_once implode(DIRECTORY_SEPARATOR, array($rootdir, "vendor", "autoload.php"));

$app = new holonet\holofw\FWApplication($rootdir, "sc2rep");
$request = holonet\http\HttpRequest::createFromGlobals();
$app->handle($request)->send();
