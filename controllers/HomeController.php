<?php
/**
* This file is part of the sc2rep project
 * (c) Matthias Lantsch
 *
 * Class file for the HomeController
 *
 * @package sc2rep
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\sc2rep\controllers;

use holonet\holofw\FWController;
use holonet\sc2rep\models\TagModel;
use holonet\sc2rep\models\MapModel;
use holonet\sc2rep\models\SeasonModel;
use holonet\sc2rep\models\PlayerModel;
use holonet\sc2rep\models\PerformanceModel;
use holonet\sc2rep\helpers\MatchFilter;

/**
 * HomeController giving the user access to certain general purpose pages
 *
 * @author  matthias.lantsch
 * @package holonet\sc2rep\controllers
 */
class HomeController extends FWController {

	/**
	 * GET / (root homepage)
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function index() {
		yield "title" => "SC2REP replay analysis tool";
	}

	/**
	 * method for the tag action
	 * GET [group:]/[name:]
	 *
	 * @access public
	 * @param  string $group The group of the tag to display a page for
	 * @param  string $name The name of the tag to display a page for
	 * @return the yield from the controller method
	 */
	public function tag(string $group, string $name) {
		$group = urldecode($group);
		$name = urldecode($name);
		if(($tag = TagModel::get(array("group" => $group, "name" => $name))) === null) {
			$this->notFound("Could not find tag with the group '{$group}' and name '{$name}'");
		}

		yield "title" => "SC2REP - {$tag->name}";
		yield "tag" => $tag;
		yield "seasons" => SeasonModel::all();

		$this->renderTemplate("match".DIRECTORY_SEPARATOR."filter");
	}

	/**
	 * api method to get a list of maps
	 * GET /api/maps?term
	 * AJAX
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function mapApi() {
		if(($searchterm = $this->request->query->get("term", "")) !== "") {
			$ret = [];
			foreach(MapModel::select(["denied" => false, "tag.name[~]" => $searchterm]) as $map) {
				$ret[] = [
					"id" => $map->id,
					"label" => $map->name,
					"value" => $map->name
				];
			}
			yield "maps" => $ret;
		} else {
			yield "maps" => [];
		}

		$this->respondTo("json")->addCallback(function($data) {return $data["maps"];});
	}

	/**
	 * api method to get a list of pro players
	 * GET /api/pros?term
	 * AJAX
	 *
	 * @access public
	 * @return the yield from the controller method
	 */
	public function prosApi() {
		$ret = [];
		foreach(TagModel::select(["group" => "player", "name[~]" => $this->request->query->get("term", "")]) as $pro) {
			$ret[] = [
				"id" => $pro->id,
				"label" => $pro->name,
				"value" => $pro->name
			];
		}
		yield "pros" => $ret;

		$this->respondTo("json")->addCallback(function($data) {return $data["pros"];});
	}
}
