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

use HIS5\lib\activerecord\Schema as Schema;

##
## buildType #
##
Schema::createTable('buildType', function($t) {
	$t->string("name")->setSizeDef('40');
	$t->version("1460100322");
});

##
## match #
##
Schema::createTable('match', function($t) {
	$t->string("identifier")->setSizeDef('255');
	$t->boolean("isLadder");
	$t->integer("loops");
	$t->timestamp("played")->setDefault('CURRENT_TIMESTAMP');
	$t->integer("length");
	$t->version("1460159150");
});

##
## performance #
##
Schema::createTable('performance', function($t) {
	$t->integer("RCR")->nullable();
	$t->integer("SQ")->nullable();
	$t->integer("AU")->nullable();
	$t->integer("APM")->nullable();
	$t->boolean("isWin")->nullable();
	$t->integer("team");
	$t->integer("secondBase")->nullable();
	$t->integer("thirdBase")->nullable();
	$t->integer("onebaseSat")->nullable();
	$t->integer("twobaseSat")->nullable();
	$t->integer("threebaseSat")->nullable();
	$t->integer("workerCount")->nullable();
	$t->integer("sid");
	$t->string("divisionRank")->setSizeDef('20')->nullable();
	$t->string("leagueRank")->setSizeDef('20')->nullable();
	$t->string("serverRank")->setSizeDef('20')->nullable();
	$t->string("globalRank")->setSizeDef('20')->nullable();
	$t->integer("points")->nullable();
	$t->integer("winrate")->nullable();
	$t->version("1462186172");
});

##
## permission #
##
Schema::createTable('permission', function($t) {
	$t->string("permission")->setSizeDef('40');
	$t->version("1449494964");
});

##
## player #
##
Schema::createTable('player', function($t) {
	$t->string("name")->setSizeDef('45');
	$t->string("clantag")->setSizeDef('10');
	$t->string("url")->setSizeDef('255');
	$t->string("bnet")->setSizeDef('10');
	$t->string("portrait")->setSizeDef('255')->nullable();
	$t->string("curLeague")->setSizeDef('15')->nullable();
	$t->version("1462802284");
});

##
## status #
##
Schema::createTable('status', function($t) {
	$t->string("name")->setSizeDef('10');
	$t->version("1460100321");
});

##
## step #
##
Schema::createTable('step', function($t) {
	$t->integer("supply");
	$t->integer("time");
	$t->integer("order");
	$t->string("asset")->setSizeDef('40');
	$t->string("descBrief")->setSizeDef('40')->nullable();
	$t->string("descLonger")->setSizeDef('255')->nullable();
	$t->boolean("chrono");
	$t->version("1463119797");
});

##
## stepType #
##
Schema::createTable('stepType', function($t) {
	$t->string("name")->setSizeDef('10');
	$t->version("1463119795");
});

##
## tag #
##
Schema::createTable('tag', function($t) {
	$t->string("name")->setSizeDef('255');
	$t->string("group")->setSizeDef('40');
	$t->version("1460100310");
});

##
## user #
##
Schema::createTable('user', function($t) {
	$t->string("name")->setSizeDef('40');
	$t->string("email")->setSizeDef('40');
	$t->string("authHash")->setSizeDef('255')->nullable();
	$t->version("1449494962");
});

##
## userGroup #
##
Schema::createTable('userGroup', function($t) {
	$t->string("name")->setSizeDef('40');
	$t->version("1449494963");
});

##
## build #
##
Schema::createExtendsTable('build', 'tag', function($t) {
	$t->string("name")->setSizeDef('255');
	$t->string("descBrief")->setSizeDef('255')->nullable();
	$t->text("descLonger")->nullable();
	$t->pkey("idTag");
	$t->version("1463122905");
});

##
## map #
##
Schema::createExtendsTable('map', 'tag', function($t) {
	$t->string("identifier")->setSizeDef('255');
	$t->integer("sizeX")->nullable();
	$t->integer("sizeY")->nullable();
	$t->boolean("denied");
	$t->pkey("idTag");
	$t->version("1463642099");
});

##
## match2tag #
##
Schema::createResolutionTable('match', 'tag', '1460100421');


##
## permission2user #
##
Schema::createResolutionTable('permission', 'user', '1449494965');


##
## permission2userGroup #
##
Schema::createResolutionTable('permission', 'userGroup', '1449494967');


##
## race #
##
Schema::createExtendsTable('race', 'tag', function($t) {
	$t->boolean("isPlayable");
	$t->pkey("idTag");
	$t->version("1460100324");
});

##
## season #
##
Schema::createExtendsTable('season', 'tag', function($t) {
	$t->datetime("start");
	$t->datetime("lock")->nullable();
	$t->datetime("end")->nullable();
	$t->integer("number");
	$t->pkey("idTag");
	$t->version("1460100325");
});

##
## user2userGroup #
##
Schema::createResolutionTable('user', 'userGroup', '1449494966');


##
## build references #
##
Schema::changeTable('build', function($t) {
	$t->addReference("tag", "idTag", "idTag");
	$t->addReference("buildType", "idBuildType", "idBuildType");
	$t->addReference("user", "idUser", "idUser");
	$t->version("1463122905");
});

##
## map references #
##
Schema::changeTable('map', function($t) {
	$t->addReference("tag", "idTag", "idTag");
	$t->version("1463642099");
});

##
## match references #
##
Schema::changeTable('match', function($t) {
	$t->addReference("status", "idStatus", "idStatus");
	$t->version("1460159150");
});

##
## performance references #
##
Schema::changeTable('performance', function($t) {
	$t->addReference("match", "idMatch", "idMatch");
	$t->addReference("player", "idPlayer", "idPlayer");
	$t->addReference("race", "pickRace", "idTag");
	$t->addReference("race", "playRace", "idTag");
	$t->addReference("tag", "league", "idTag")->nullable();
	$t->version("1462186172");
});

##
## player references #
##
Schema::changeTable('player', function($t) {
	$t->addReference("user", "idUser", "idUser")->nullable();
	$t->version("1462802284");
});

##
## race references #
##
Schema::changeTable('race', function($t) {
	$t->addReference("tag", "idTag", "idTag");
	$t->version("1460100324");
});

##
## season references #
##
Schema::changeTable('season', function($t) {
	$t->addReference("tag", "idTag", "idTag");
	$t->version("1460100325");
});

##
## step references #
##
Schema::changeTable('step', function($t) {
	$t->addReference("build", "idBuild", "idTag");
	$t->addReference("stepType", "type", "idStepType");
	$t->version("1463119797");
});