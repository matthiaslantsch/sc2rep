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
Router::any([
	"url" => "player/[idPlayer:i]",
	"controller" => "home",
	"method" => "player"
]);
