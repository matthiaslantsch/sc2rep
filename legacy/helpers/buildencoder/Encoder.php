<?php
/**
 * This file is part of the sc2rep replay parser project
 * (c) Matthias Lantsch
 *
 * class file for the build encoder parent interface
 */

namespace HIS5\sc2rep\helpers\buildencoder;

use HIS5\sc2rep\models as models;

/**
 * parent interface for the format encoder classes doing the actual encoding
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\sc2rep\helpers\buildencoder
 */
interface Encoder {

	public static function encode(models\BuildModel $build);
	public static function decode($parameter);

}