<?php
# This file should contain all the record creation needed to seed the database with its default values.
# The data can then be loaded with the db/schema::seed or with the complete db/schema::setup task
#
# Examples:
#
#   $stones = models\StoneModel::create(array("name" => "a stone"));

use HIS5\sc2rep\models as models;
use HIS5\holoFW\models as hmodels;

models\TagModel::create([
	"name" => "1v1",
	"group" => "gametype"
], true);

models\TagModel::create([
	"name" => "2v2",
	"group" => "gametype"
], true);

models\TagModel::create([
	"name" => "3v3",
	"group" => "gametype"
], true);

models\TagModel::create([
	"name" => "4v4",
	"group" => "gametype"
], true);

models\TagModel::create([
	"name" => "Archon",
	"group" => "gametype"
], true);

models\TagModel::create([
	"name" => "FFA",
	"group" => "gametype"
], true);

models\TagModel::create([
	"name" => "PvP",
	"group" => "matchup"
], true);

models\TagModel::create([
	"name" => "PvT",
	"group" => "matchup"
], true);

models\TagModel::create([
	"name" => "PvZ",
	"group" => "matchup"
], true);

models\TagModel::create([
	"name" => "TvZ",
	"group" => "matchup"
], true);

models\TagModel::create([
	"name" => "TvZ",
	"group" => "matchup"
], true);

models\TagModel::create([
	"name" => "ZvZ",
	"group" => "matchup"
], true);

models\TagModel::create([
	"name" => "Protoss",
	"group" => "race",
	"race" => new models\RaceModel([
		"isPlayable" => true
	])
], true);

models\TagModel::create([
	"name" => "Terran",
	"group" => "race",
	"race" => new models\RaceModel([
		"isPlayable" => true
	])
], true);

models\TagModel::create([
	"name" => "Zerg",
	"group" => "race",
	"race" => new models\RaceModel([
		"isPlayable" => true
	])
], true);

models\TagModel::create([
	"name" => "Random",
	"group" => "race",
	"race" => new models\RaceModel([
		"isPlayable" => false
	])
], true);

models\BuildTypeModel::create([
	"name" => "opening"
], true);

models\BuildTypeModel::create([
	"name" => "cheese"
], true);

models\BuildTypeModel::create([
	"name" => "timing attack"
], true);

models\StatusModel::create(["name" => "imported"], true);
models\StatusModel::create(["name" => "processing"], true);
models\StatusModel::create(["name" => "done"], true);

models\PlayerModel::create([
	"name" => "Computer",
	"clantag" => "",
	"url" => "",
	"bnet" => 0
], true);

models\StepTypeModel::create(["name" => "army"], true);
models\StepTypeModel::create(["name" => "worker"], true);
models\StepTypeModel::create(["name" => "struct"], true);
models\StepTypeModel::create(["name" => "upgrade"], true);
models\StepTypeModel::create(["name" => "morph"], true);

require_once "seasons.php";
//require_once "gate_expand.php";

/*models\MatchModel::create([
		"identifier" => "test",
		"isLadder" => 1,
		"played" => date("Y-m-d H:i:s", strtotime("5-2-2016")),
		"length" => 10,
		"loops" => 5,
		"idStatus" => 3,
		"tag" => [
			models\TagModel::findOrCreateTag("myMap", "map"),
			models\TagModel::findOrCreateTag("PvT", "matchup"),
			models\TagModel::findOrCreateTag("1v1", "gametype")
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
			models\TagModel::findOrCreateTag("myMap", "map"),
			models\TagModel::findOrCreateTag("PvT", "matchup"),
			models\TagModel::findOrCreateTag("1v1", "gametype")
		],
		"performance" => [
			new models\PerformanceModel([
				"player" => $pl,
				"team" => 0,
				"pickRace" => models\TagModel::findBy("name = 'Random'")->id,
				"playRace" => models\TagModel::findBy("name = 'Protoss'")->id,
				"sid" => 0,
				"isWin" => ($i % 2 == 0),
				"APM" => $i,
				"SQ" => $i
			])
		]
	]);
}*/