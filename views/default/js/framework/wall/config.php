<?php

/**
 * Add session geopositioning data to the config
 */
namespace hypeJunction\Wall;

if (!WALL_GEOPOSITIONING) {
	$geopositioning = get_geopositioning();
	echo 'elgg.session.geopositioning = ' . json_encode($geopositioning) . ';';
}
