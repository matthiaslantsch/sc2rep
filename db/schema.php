<?php
# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.php definition is the main source for your
# database schema. To recreate the database, do not run all migrations, use the
# db/schema::load task
#
# It's strongly recommended that you check this file into your version control system.

use holonet\activerecord\Schema;

##
## tag #
##
Schema::createTable("tag", function($table) {
	$table->string("name");
	$table->string("group", 40);
	$table->version("1460100310");
});

##
## status #
##
Schema::createTable("status", function($table) {
	$table->string("name", 10);
	$table->version("1460100311");
});

##
## match #
##
Schema::createExtendsTable("match", "match", function($table) {
	$table->string("identifier");
	$table->boolean("isLadder");
	$table->integer("loops");
	$table->timestamp("played");
	$table->integer("length");
	$table->integer("idStatus");
	$table->integer("idPlayer");
	$table->integer("pickRace");
	$table->integer("playRace");
	$table->integer("league")->nullable();
	$table->version("1460100312");
});

##
## player #
##
Schema::createTable("player", function($table) {
	$table->string("name", 45);
	$table->string("clantag", 10);
	$table->string("url");
	$table->string("bnet", 10);
	$table->string("portrait")->nullable();
	$table->string("curLeague", 15)->nullable();
	$table->version("1460100313");
});

##
## race #
##
Schema::createExtendsTable("race", "tag", function($table) {
	$table->boolean("isPlayable");
	$table->version("1460100314");
});

##
## map #
##
Schema::createExtendsTable("map", "tag", function($table) {
	$table->string("identifier");
	$table->integer("sizeX")->nullable();
	$table->integer("sizeY")->nullable();
	$table->boolean("denied");
	$table->version("1460100315");
});

##
## season #
##
Schema::createExtendsTable("season", "tag", function($table) {
	$table->datetime("start");
	$table->datetime("lock")->nullable();
	$table->datetime("end")->nullable();
	$table->integer("number");
	$table->version("1460100316");
});

##
## performance #
##
Schema::createExtendsTable("performance", "tag", function($table) {
	$table->integer("RCR")->nullable();
	$table->integer("SQ")->nullable();
	$table->integer("AU")->nullable();
	$table->integer("APM")->nullable();
	$table->boolean("isWin")->nullable();
	$table->integer("team");
	$table->integer("secondBase")->nullable();
	$table->integer("thirdBase")->nullable();
	$table->integer("onebaseSat")->nullable();
	$table->integer("twobaseSat")->nullable();
	$table->integer("threebaseSat")->nullable();
	$table->integer("workerCount")->nullable();
	$table->integer("sid");
	$table->string("divisionRank", 20)->nullable();
	$table->string("leagueRank", 20)->nullable();
	$table->string("serverRank", 20)->nullable();
	$table->string("globalRank", 20)->nullable();
	$table->integer("points")->nullable();
	$table->integer("winrate")->nullable();
	$table->integer("number");
	$table->integer("idMatch");
	$table->integer("idPlayer");
	$table->integer("pickRace");
	$table->integer("playRace");
	$table->integer("league")->nullable();
	$table->version("1460100317");
});

##
## match2tag #
##
Schema::createResolutionTable("match", "tag", "1460100318");

##
## match references #
##
Schema::changeTable("match", function($table) {
	$table->addReference("status", "idStatus", "idStatus");
	$table->addReference("player", "idPlayer", "idPlayer");
	$table->addReference("race", "pickRace", "idTag");
	$table->addReference("race", "playRace", "idTag");
	$table->addReference("tag", "league", "idTag");
	$table->version("1460100312");
});

##
## performance references #
##
Schema::changeTable("performance", function($table) {
	$table->addReference("match", "idMatch", "idMatch");
	$table->addReference("player", "idPlayer", "idPlayer");
	$table->addReference("race", "pickRace", "idTag");
	$table->addReference("race", "playRace", "idTag");
	$table->version("1460100317");
});