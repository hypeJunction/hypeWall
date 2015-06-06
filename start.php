<?php

/**
 * User Walls
 *
 * @package hypeJunction
 * @subpackage Wall
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 */
try {
	require_once __DIR__ . '/lib/autoloader.php';
	hypeWall()->boot();
} catch (Exception $ex) {
	register_error($ex->getMessage());
}