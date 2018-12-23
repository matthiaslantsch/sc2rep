<?php
# This file is used to populate the seasons database table
# This contains a hardcoded version of the versions of sc2 ranked.
# The data can then be loaded with the db/schema::seed or with the complete db/schema::setup task
#
# Examples:
#
#   $stones = models\StoneModel.create(array("name" => "a stone"));

use holonet\sc2rep\models\SeasonModel;

echo "Importing season data\n";

SeasonModel::create(array(
	"name" => "2013 Season 2",
	"group" => "season",
	"number" => 12,
	"start" => date("Y-m-d H:i:s", strtotime("11 Mar 2013")),
	"lock" => date("Y-m-d H:i:s", strtotime("24 Apr 2013")),
	"end" => date("Y-m-d H:i:s", strtotime("1 May 2013"))
), true);

SeasonModel::create(array(
	"name" => "2013 Season 3",
	"group" => "season",
	"number" => 13,
	"start" => date("Y-m-d H:i:s", strtotime("1 May 2013")),
	"end" => date("Y-m-d H:i:s", strtotime("10 Jun 2013"))
), true);

SeasonModel::create(array(
	"name" => "2013 Season 4",
	"group" => "season",
	"number" => 14,
	"start" => date("Y-m-d H:i:s", strtotime("10 Jun 2013")),
	"lock" => date("Y-m-d H:i:s", strtotime("19 Aug 2013")),
	"end" => date("Y-m-d H:i:s", strtotime("26 Aug 2013"))
), true);

SeasonModel::create(array(
	"name" => "2013 Season 5",
	"group" => "season",
	"number" => 15,
	"start" => date("Y-m-d H:i:s", strtotime("26 Aug 2013")),
	"lock" => date("Y-m-d H:i:s", strtotime("4 Nov 2013")),
	"end" => date("Y-m-d H:i:s", strtotime("11 Nov 2013"))
), true);

SeasonModel::create(array(
	"name" => "2013 Season 6",
	"group" => "season",
	"number" => 16,
	"start" => date("Y-m-d H:i:s", strtotime("11 Nov 2013")),
	"lock" => date("Y-m-d H:i:s", strtotime("27 Dec 2013")),
	"end" => date("Y-m-d H:i:s", strtotime("3 Jan 2014"))
), true);

SeasonModel::create(array(
	"name" => "2014 Season 1",
	"group" => "season",
	"number" => 17,
	"start" => date("Y-m-d H:i:s", strtotime("3 Jan 2014")),
	"lock" => date("Y-m-d H:i:s", strtotime("7 Apr 2014")),
	"end" => date("Y-m-d H:i:s", strtotime("14 Apr 2014"))
), true);

SeasonModel::create(array(
	"name" => "2014 Season 2",
	"group" => "season",
	"number" => 18,
	"start" => date("Y-m-d H:i:s", strtotime("14 Apr 2014")),
	"lock" => date("Y-m-d H:i:s", strtotime("23 Jun 2014")),
	"end" => date("Y-m-d H:i:s", strtotime("7 Jul 2014"))
), true);

SeasonModel::create(array(
	"name" => "2014 Season 3",
	"group" => "season",
	"number" => 19,
	"start" => date("Y-m-d H:i:s", strtotime("7 Jul 2014")),
	"lock" => date("Y-m-d H:i:s", strtotime("04 Nov 2014")),
	"end" => date("Y-m-d H:i:s", strtotime("10 Nov 2014"))
), true);

SeasonModel::create(array(
	"name" => "2014 Season 4 ",
	"group" => "season",
	"number" => 20,
	"start" => date("Y-m-d H:i:s", strtotime("10 Nov 2014")),
	"lock" => date("Y-m-d H:i:s", strtotime("05 Jan 2015")),
	"end" => date("Y-m-d H:i:s", strtotime("12 Jan 2015"))
), true);

SeasonModel::create(array(
	"name" => "2015 Season 1",
	"group" => "season",
	"number" => 21,
	"start" => date("Y-m-d H:i:s", strtotime("12 Jan 2015")),
	"lock" => date("Y-m-d H:i:s", strtotime("06 Apr 2015")),
	"end" => date("Y-m-d H:i:s", strtotime("13 Apr 2015"))
), true);

SeasonModel::create(array(
	"name" => "2015 Season 2",
	"group" => "season",
	"number" => 22,
	"start" => date("Y-m-d H:i:s", strtotime("13 Apr 2015")),
	"lock" => date("Y-m-d H:i:s", strtotime("22 Jun 2015")),
	"end" => date("Y-m-d H:i:s", strtotime("29 Jun 2015"))
), true);

SeasonModel::create(array(
	"name" => "2015 Season 3",
	"group" => "season",
	"number" => 23,
	"start" => date("Y-m-d H:i:s", strtotime("29 Jun 2015")),
	"lock" => date("Y-m-d H:i:s", strtotime("3 Nov 2015 ")),
	"end" => date("Y-m-d H:i:s", strtotime("10 Nov 2015 "))
), true);

SeasonModel::create(array(
	"name" => "2015 Season 4",
	"group" => "season",
	"number" => 24,
	"start" => date("Y-m-d H:i:s", strtotime("9 Nov 2015 ")),
	"lock" => date("Y-m-d H:i:s", strtotime("24 Jan 2016")),
	"end" => date("Y-m-d H:i:s", strtotime("31 Jan 2016"))
), true);

SeasonModel::create(array(
	"name" => "2016 Season 1",
	"group" => "season",
	"number" => 25,
	"start" => date("Y-m-d H:i:s", strtotime("31 Jan 2016")),
	"lock" => date("Y-m-d H:i:s", strtotime("21 Mar 2016")),
	"end" => date("Y-m-d H:i:s", strtotime("28 Mar 2016"))
), true);

SeasonModel::create(array(
	"name" => "2016 Season 2",
	"group" => "season",
	"number" => 26,
	"start" => date("Y-m-d H:i:s", strtotime("28 Mar 2016")),
	"lock" => date("Y-m-d H:i:s", strtotime("13 Jun 2016")),
	"end" => date("Y-m-d H:i:s", strtotime("20 Jun 2016"))
), true);

SeasonModel::create(array(
	"name" => "2016 Season 3",
	"group" => "season",
	"number" => 27,
	"start" => date("Y-m-d H:i:s", strtotime("20 Jun 2016")),
	"lock" => date("Y-m-d H:i:s", strtotime("9 Jul 2016")),
	"end" => date("Y-m-d H:i:s", strtotime("12 Jul 2016"))
), true);

SeasonModel::create(array(
	"name" => "2016 Season 4",
	"group" => "season",
	"number" => 28,
	"start" => date("Y-m-d H:i:s", strtotime("12 Jul 2016")),
	"lock" => date("Y-m-d H:i:s", strtotime("11 Oct 2016")),
	"end" => date("Y-m-d H:i:s", strtotime("18 Oct 2016"))
), true);

SeasonModel::create(array(
	"name" => "2016 Season 5",
	"group" => "season",
	"number" => 29,
	"start" => date("Y-m-d H:i:s", strtotime("18 Oct 2016")),
	"lock" => date("Y-m-d H:i:s", strtotime("16 Nov 2016")),
	"end" => date("Y-m-d H:i:s", strtotime("23 Nov 2016"))
), true);

SeasonModel::create(array(
	"name" => "2016 Season 6",
	"group" => "season",
	"number" => 30,
	"start" => date("Y-m-d H:i:s", strtotime("23 Nov 2016")),
	"end" => date("Y-m-d H:i:s", strtotime("26 Jan 2017"))
), true);

SeasonModel::create(array(
	"name" => "2017 Season 1",
	"group" => "season",
	"number" => 31,
	"start" => date("Y-m-d H:i:s", strtotime("26 Jan 2017"))
), true);
