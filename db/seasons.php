<?php
# This file is used to populate the seasons database table
# This contains a hardcoded version of the versions of sc2 ranked.
# The data can then be loaded with the db/schema::seed or with the complete db/schema::setup task
#
# Examples:
#  .*?[^|]+\|(B\d|\d+)[^|]?\|\|\|?([\d-\w ]+| - )[^|]?\|\|[^|]?([\d-\w ]+| - )[^|]+\|\|[^|]?([\d-\w ]+| - )[^\n]+\|\|([\d-\w ]+| - )[^|]?
#   $stones = models\StoneModel.create(array("name" => "a stone"));

use holonet\sc2rep\models\SeasonModel;

$seasonsCSV = <<<SEASONS
1, 2010 Season 7 , 7 Jul 2010, 22 Mar 2011,  29 Mar 2011
2, 2011 Season 1 , 29 Mar 2011, 5 Jul 2011, 26 Jul 2011
3, 2011 Season 2 , 26 Jul 2011, 10 Oct 2011, 24 Oct 2011
4, 2011 Season 3 , 4 Oct 2011, 13 Dec 2011, 19 Dec 2011
5, 2011 Season 4, 9 Dec 2011, 14 Feb 2012, 20 Feb 2012
6, 2012 Season 1, 20 Feb 2011, 3 Apr 2012, 9 Apr 2012
7, 2012 Season 2,  Apr 2012, 5 Jun 2012, 11 Jun 2012
8, 2012 Season 3 , 1 Jun 2012,  Sep 2012, 11 Sep 2012
9, 2012 Season 4 , 11 Sep 2012, 4 Oct 2012,  1 Nov 2012
10, 2012 Season 5,  Nov 2012, 7 Dec 2012,  3 Jan 2013
11, 2013 Season 1,  Jan 2013,  Mar 2013, 11 Mar 2013
12, 2013 Season 2, 1 Mar 2013, 4 Apr 2013, 1 May 2013
13, 2013 Season 3,  May 2013,  Jan 2013, 10 Jun 2013
14, 2013 Season 4, 0 Jun 2013, 9 Aug 2013, 26 Aug 2013
15, 2013 Season 5, 6 Aug 2013,  Nov 2013, 11 Nov 2013
16, 2013 Season 6, 1 Nov 2013, 7 Dec 2013,  3 Jan 2014
17, 2014 Season 1 , 3 Jan 2014, 7 Apr 2014,  14 Apr 2014
18, 2014 Season 2 , 14 Apr 2014, 23 Jun 2014,  7 Jul 2014
19, 2014 Season 3 , 7 Jul 2014, 04 Nov 2014,  10 Nov 2014
20, 2014 Season 4 , 10 Nov 2014, 05 Jan 2015,  12 Jan 2015
21, 2015 Season 1 , 12 Jan 2015, 06 Apr 2015,  13 Apr 2015
22, 2015 Season 2 , 13 Apr 2015, , 22 Jun 2015
23, 2015 Season 3 , 29 Jun 2015, , 3 Nov 2015
24, 2015 Season 4 , 9 Nov 2015, 24 Jan 2016,  31 Jan 2016
25, 2016 Season 1 , 31 Jan 2016, , 21 Mar 2016,
26, 2016 Season 2 , 28 Mar 2016, , 13 Jun 2016,
27, 2016 Season 3 , 20 Jun 2016, , 9 Jul 2016,
28, 2016 Season 4 , 12 Jul 2016, 11 Oct 2016,  18 Oct 2016
29, 2016 Season 5 , 18 Oct 2016, 15 Nov 2016,  22 Nov 2016
30, 2016 Season 6 , 22 Nov 2016, 17 Jan 2016,  24 Jan 2017
31, 2017 Season 1 , 24 Jan 2017, 25 April 2017,  2 May 2017
32, 2017 Season 2 , 2 May 2017,  ,19 July 2017
33, 2017 Season 3 , 19 July 2017,  ,20 October 2017
34, 2017 Season 4 , 20 October 2017,  ,23 January 2018
35, 2018 Season 1 ,  23 January 2018, 8 May 2018,  15 May 2018
36, 2018 Season 2 , 15 May 2018, 7 Aug 2018,  14 Aug 2018
37, 2018 Season 3 , 14 Aug 2018, 13 Nov 2018,  20 Nov 2018
38,2018 Season 4,20 Nov 2018
SEASONS;

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
	"start" => date("Y-m-d H:i:s", strtotime("24 Jan 2017")),
	"lock" => date("Y-m-d H:i:s", strtotime("25 April 2017")),
	"end" => date("Y-m-d H:i:s", strtotime("2 May 2017")),
), true);

SeasonModel::create(array(
	"name" => "2017 Season 2",
	"group" => "season",
	"number" => 32,
	"start" => date("Y-m-d H:i:s", strtotime("2 May 2017")),
	"end" => date("Y-m-d H:i:s", strtotime("19 July 2017")),
), true);

SeasonModel::create(array(
	"name" => "2017 Season 3",
	"group" => "season",
	"number" => 33,
	"start" => date("Y-m-d H:i:s", strtotime("19 July 2017")),
	"end" => date("Y-m-d H:i:s", strtotime("20 October 2017")),
), true);

SeasonModel::create(array(
	"name" => "2017 Season 4",
	"group" => "season",
	"number" => 34,
	"start" => date("Y-m-d H:i:s", strtotime("20 October 2017")),
	"end" => date("Y-m-d H:i:s", strtotime("23 January 2018")),
), true);

SeasonModel::create(array(
	"name" => "2018 Season 1",
	"group" => "season",
	"number" => 35,
	"start" => date("Y-m-d H:i:s", strtotime("23 January 2018")),
	"lock" => date("Y-m-d H:i:s", strtotime("8 May 2018")),
	"end" => date("Y-m-d H:i:s", strtotime("15 May 2018")),
), true);

SeasonModel::create(array(
	"name" => "2018 Season 2",
	"group" => "season",
	"number" => 36,
	"start" => date("Y-m-d H:i:s", strtotime("15 May 2018")),
	"lock" => date("Y-m-d H:i:s", strtotime("7 Aug 2018")),
	"end" => date("Y-m-d H:i:s", strtotime("14 Aug 2018")),
), true);

SeasonModel::create(array(
	"name" => "2018 Season 3",
	"group" => "season",
	"number" => 37,
	"start" => date("Y-m-d H:i:s", strtotime("14 Aug 2018")),
	"lock" => date("Y-m-d H:i:s", strtotime("13 Nov 2018")),
	"end" => date("Y-m-d H:i:s", strtotime("20 Nov 2018")),
), true);

SeasonModel::create(array(
	"name" => "2018 Season 3",
	"group" => "season",
	"number" => 38,
	"start" => date("Y-m-d H:i:s", strtotime("20 Nov 2018"))
), true);
