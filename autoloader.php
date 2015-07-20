<?php

if (!is_callable('hypeApps')) {
	throw new Exception("hypeWall requires hypeApps");
}

$path = __DIR__;

if (!file_exists("{$path}/vendor/autoload.php")) {
	throw new Exception('hypeWall can not resolve composer dependencies. Run composer install');
}

require_once "{$path}/vendor/autoload.php";

/**
 * Plugin container
 * @return \hypeJunction\Wall\Plugin
 */
function hypeWall() {
	return \hypeJunction\Wall\Plugin::factory();
}