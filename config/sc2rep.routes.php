<?php
/**
 * This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * php route definition file
 *
 * @package sc2rep
 * @license http://www.wtfpl.net/ Do what the fuck you want Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

use holonet\holofw\FWRouter;

FWRouter::index(array(
	"controller" => "home",
	"method" => "index"
));

//SPECIFIC PLAYER PAGE
FWRouter::get(array(
	"url" => "player/[idPlayer:i]",
	"controller" => "player",
	"method" => "player"
));

//GET PLAYER PROFILE DATA VIA JSON
FWRouter::get(array(
	"url" => "player/[idPlayer:i]/profileData",
	"controller" => "player",
	"method" => "profileData"
));

//Backend page to upload replay files to
FWRouter::post(array(
	"url" => "upload",
	"controller" => "file",
	"method" => "upload"
));

/**
 * The matches controller exposing match pages
 */
FWRouter::with("matches", function($builder) {
	//MATCHES LISTING PAGE
	$builder->index(array(
		"controller" => "match",
		"method" => "matches"
	));
	//MATCH SHOW PAGE
	$builder->get(array(
		"url" => "[idMatch:i]",
		"controller" => "match",
		"method" => "show"
	));
	//Backend page to download replay files from
	$builder->get(array(
		"url" => "[idMatch:i]/download",
		"controller" => "file",
		"method" => "download"
	));
	//ALLOW THE CLIENT TO LOAD DETAIL DATA ABOUT A MATCH
	$builder->get(array(
		"url" => "[idMatch:i]/[dataPack:]",
		"controller" => "match",
		"method" => "loadData"
	));
});

/**
 * The api backend allowing the js client to load data
 */
FWRouter::with("api", function($builder) {
	//API TO RETURN SUGGESTIONS FOR A PLAYER
	$builder->get(array(
		"url" => "players",
		"controller" => "player",
		"method" => "playersApi"
	));
	//API TO RETURN MAP SUGGESTIONS FOR A SEARCH TERM
	$builder->get(array(
		"url" => "maps",
		"controller" => "home",
		"method" => "mapApi"
	));
	//API TO RETURN A LISTING OF MATCHES MATCHING CRITERIA
	$builder->get(array(
		"url" => "matches",
		"controller" => "match",
		"method" => "matchApi"
	));
	//API TO RETURN A LISTING OF PRO PLAYERS FOR A SEARCH TERM
	$builder->get(array(
		"url" => "pros",
		"controller" => "home",
		"method" => "prosApi"
	));
});

//SPECIFIC TAG PAGE
//allows for a special page e.g. maps
//at the end because otherwise all urls would match this
FWRouter::get(array(
	"url" => "[group:]/[name:]",
	"controller" => "home",
	"method" => "tag"
));
