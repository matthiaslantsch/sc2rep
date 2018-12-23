<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the BnetprofileMiner crawler class
 */

namespace HIS5\sc2rep\helpers;

/**
 * helper class used to crawl a battle.net profile for user information
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\helpers
 */
class BnetprofileMiner {

	/**
	 * property containing the url of the profile to crawl onto
	 *
	 * @access 	public
	 * @var 	string url | url of the profile to be mined
	 */
	public $url;

	/**
	 * property containing a simplexml object of the webpage
	 *
	 * @access 	public
	 * @var 	simplexml object | simplexml object of the webpage
	 */
	public $simplexml;

	/**
	 * property containing the mined data of the profile
	 *
	 * @access 	public
	 * @var 	array data | data from the profile
	 */
	public $data = [];

	/**
	 * constructor method
	 *
	 * @access public
	 * @param  string url | url of the profile to be mined
	 */
	public function __construct($url) {
		$this->url = $url."ladder/league";
	}

	/**
	 * method actually mining the profile
	 *
	 * @access public
	 * @return array with player information
	 */
	public function mineProfile() {

		if($this->url === "ladder/league") {
			return;
		}

		$response = HttpGetter::request($this->url);
		if($response === false) {
			return [];
		}

		$this->simpleXml = simplexml_load_string($response);

		if($this->simpleXml === false) {
			return [];
		}

		$this->getLeagues();
		$this->getPortrait();

		//update the name/clantag
		$nameTag = $this->simpleXml->xpath("//*[@class='user-name']");
		if(isset($nameTag[0])) {
			$this->data["clantag"] = trim(strval($nameTag[0]->span), "[]");
			$this->data["name"] = trim(strval($nameTag[0]->a));
		}

		return $this->data;
	}

	/**
	 * method parsing out the league on the simplexml object
	 *
	 * @access private
	 */
	private function getLeagues() {
		$rankTag = $this->simpleXml->xpath("//*[@id='profile-menu']/*[contains(@class,'submenu')]/*");
		if(!isset($rankTag[0])) {
			return;
		}

		$this->data["leagues"] = [];
		$pattern = "/".
					"(?<=badge-)". //lookbehind to find the league in the class
						"(\w+)". //capturing group for the league
					"(?= badge-)". //lookahead after the league class
					"(?:[^']*?)". //non-capturing lazy group to throw away the stuff in between
					"(?<=\/>)". //lookbehind to get the text after the <br /> tag
					"([^<]+)/"; //capturing group for the rank participants

		foreach ($rankTag as $rank) {
			preg_match('/(\dv\d)/', $rank->__toString(), $m);

			$ladderRanking =
				$this->simpleXml->xpath("//*[@id='".ltrim($rank->attributes()->{'data-tooltip'}, '#')."']/*[contains(@class,'ladder-tooltip')]")[0]->asXml();

			preg_match($pattern, $ladderRanking, $matches);

			//identifier: {mode}-{participent1}-{participent2}
			$leagueIdentifier = "{$m[1]}";
			$participents = explode(',', $matches[2]);
			sort($participents);
			foreach ($participents as $participent) {
				$leagueIdentifier .= "-".trim($participent);
			}

			if($m[1] == "2v2") {
				//Archon mode is still displayed as 2v2
				//for now, we will just if a second 2v2 rating pops up with the same stats, use the second one as 2v2 rating
				$archonMode = str_replace("2v2", "archon", $leagueIdentifier);
				if(!isset($this->data["leagues"][$archonMode])) {
					//since archon mode is usually the first 2v2 rating
					$leagueIdentifier = $archonMode;
				}
			}

			$this->data["leagues"][$leagueIdentifier] = $matches[1];
		}
	}

	/**
	 * method parsing out the current portrait of the player
	 *
	 * @access private
	 */
	private function getPortrait() {
		$portraitTag = $this->simpleXml->xpath("//*[@class='icon-frame ']");
		if(!isset($portraitTag[0])) {
			return;
		}
		$this->data["portrait"] = $portraitTag[0]->attributes()->style->asXml();
	}

}