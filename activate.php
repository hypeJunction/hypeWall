<?php

require_once __DIR__ . '/autoloader.php';

use hypeJunction\Wall\Post;

$subtypes = array(
	Post::SUBTYPE => Post::class,
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}