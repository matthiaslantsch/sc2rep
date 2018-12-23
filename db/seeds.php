<?php
# This file should contain all the record creation needed to seed the database with its default values.
# The data can then be loaded with the db/schema::seed or with the complete db/schema::setup task
#
# Examples:
#
#   $stones = models\StoneModel.create(array("name" => "a stone"));

use holonet\sc2rep\models\TagModel;
use holonet\sc2rep\models\RaceModel;
use holonet\sc2rep\models\StatusModel;
use holonet\sc2rep\models\PlayerModel;

StatusModel::create(["name" => "imported"], true);
StatusModel::create(["name" => "processing"], true);
StatusModel::create(["name" => "done"], true);

PlayerModel::create([
	"name" => "Computer",
	"clantag" => "",
	"url" => "",
	"bnet" => 0
], true);
TagModel::create([
	"name" => "1v1",
	"group" => "gametype"
], true);

TagModel::create([
	"name" => "2v2",
	"group" => "gametype"
], true);

TagModel::create([
	"name" => "3v3",
	"group" => "gametype"
], true);

TagModel::create([
	"name" => "4v4",
	"group" => "gametype"
], true);

TagModel::create([
	"name" => "Archon",
	"group" => "gametype"
], true);

TagModel::create([
	"name" => "FFA",
	"group" => "gametype"
], true);

TagModel::create([
	"name" => "PvP",
	"group" => "matchup"
], true);

TagModel::create([
	"name" => "PvT",
	"group" => "matchup"
], true);

TagModel::create([
	"name" => "PvZ",
	"group" => "matchup"
], true);

TagModel::create([
	"name" => "TvZ",
	"group" => "matchup"
], true);

TagModel::create([
	"name" => "TvT",
	"group" => "matchup"
], true);

TagModel::create([
	"name" => "ZvZ",
	"group" => "matchup"
], true);

RaceModel::create(array(
	"name" => "Protoss",
	"group" => "race",
	"isPlayable" => true
), true);

RaceModel::create(array(
	"name" => "Terran",
	"group" => "race",
	"isPlayable" => true
), true);

RaceModel::create(array(
	"name" => "Zerg",
	"group" => "race",
	"isPlayable" => true
), true);

RaceModel::create(array(
	"name" => "Random",
	"group" => "race",
	"isPlayable" => false
), true);

require_once "seasons.php";

//BUILD STUFF
// models\BuildTypeModel::create([
// 	"name" => "opening"
// ], true);
//
// models\BuildTypeModel::create([
// 	"name" => "cheese"
// ], true);
//
// models\BuildTypeModel::create([
// 	"name" => "timing attack"
// ], true);
// models\StepTypeModel::create(["name" => "army"], true);
// models\StepTypeModel::create(["name" => "worker"], true);
// models\StepTypeModel::create(["name" => "struct"], true);
// models\StepTypeModel::create(["name" => "upgrade"], true);
// models\StepTypeModel::create(["name" => "morph"], true);

//require_once "gate_expand.php";

//DEVELOPTMENT DATA

/*models\MatchModel::create([
		"identifier" => "test",
		"isLadder" => 1,
		"played" => date("Y-m-d H:i:s", strtotime("5-2-2016")),
		"length" => 10,
		"loops" => 5,
		"idStatus" => 3,
		"tag" => [
			TagModel::findOrCreateTag("myMap", "map"),
			TagModel::findOrCreateTag("PvT", "matchup"),
			TagModel::findOrCreateTag("1v1", "gametype")
		],
]);

$pl = models\PlayerModel::create([
	"name" => "fake",
	"clantag" => "fakeaswell",
	"url" => "allthefake",
	"bnet" => 5
]);

for($i = 0; $i < 100; $i++) {
	models\MatchModel::create([
		"identifier" => "lol-{$i}".date("Y-m-d H:i:s", strtotime("-$i minutes")),
		"isLadder" => 1,
		"played" => date("Y-m-d H:i:s", strtotime("-$i minutes")),
		"length" => 10,
		"loops" => 5,
		"idStatus" => 3,
		"tag" => [
			TagModel::findOrCreateTag("myMap", "map"),
			TagModel::findOrCreateTag("PvT", "matchup"),
			TagModel::findOrCreateTag("1v1", "gametype")
		],
		"performance" => [
			new models\PerformanceModel([
				"player" => $pl,
				"team" => 0,
				"pickRace" => TagModel::findBy("name = 'Random'")->id,
				"playRace" => TagModel::findBy("name = 'Protoss'")->id,
				"sid" => 0,
				"isWin" => ($i % 2 == 0),
				"APM" => $i,
				"SQ" => $i
			])
		]
	]);
}*/
