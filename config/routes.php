<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * procedural file used to create routing entries
 * The order matters, as the first match will be returned
 * There can only be 1 root route.
 * The first match is returned for two routes on the same url
 * example:
 * 	Router::root(array(
 *	  	"url" => "fancy/url",
 *	  	"controller" => "controller",
 *		"method" => "method"
 *	));
 */

use HIS5\holoFW\core\Router as Router;

//HOMEPAGE
Router::root([
	"controller" => "home",
	"template" => "index",
	"data" => [
		"title" => "SC2REP replay analysis tool"
	]
]);

//SPECIFIC PLAYER PAGE
Router::any([
	"url" => "player/idPlayer:int",
	"controller" => "home",
	"method" => "player"
]);

//PLAYER PROFILE DATA BACKEND
Router::any([
	"url" => "profile/idPlayer:int",
	"controller" => "home",
	"method" => "profileData"
]);

//SPECIFIC TAG PAGE
Router::any([
	"url" => "tag/idTag:int",
	"controller" => "home",
	"method" => "tag"
]);

//MATCHES OVERVIEW
Router::any([
	"url" => "matches",
	"controller" => "match",
	"method" => "matches"
]);

//SPECIFIC MATCH PAGE
Router::any([
	"url" => "match/idMatch:int",
	"controller" => "match",
	"method" => "showMatch"
]);

//BACKEND TO GET A DETAILED DATAPACK ABOUT A MATCH
Router::any([
	"url" => "loadData/idMatch:int/dataPack:string",
	"controller" => "match",
	"method" => "loadData"
]);

//BACKEND TO GET A LIST OF MAPS IN THE DATABASE
Router::any([
	"url" => "api/maps",
	"controller" => "home",
	"method" => "mapApi"
]);

//BACKEND TO GET A LIST OF PLAYERS IN THE DATABASE
Router::any([
	"url" => "api/players",
	"controller" => "home",
	"method" => "playersApi"
]);

//BACKEND TO GET A LIST OF MATCHES BASED ON A FILTER
Router::any([
	"url" => "api/matches",
	"controller" => "home",
	"method" => "matchApi"
]);

//BACKEND TO UPLOAD A REPLAY FILE
Router::post([
	"url" => "upload",
	"controller" => "file",
	"method" => "upload"
]);

//REPLAY DOWNLOAD BACKEND
Router::any([
	"url" => "download/idMatch:int",
	"controller" => "file",
	"method" => "download"
]);