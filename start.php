<?php

/**
 * User Walls
 *
 * @package hypeJunction
 * @subpackage Wall
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 */
try {
	require_once __DIR__ . '/autoloader.php';
	hypeWall()->boot();
} catch (Exception $ex) {
	register_error($ex->getMessage());
}