<?php

require_once __DIR__ . '/autoloader.php';

$subtypes = array(
	hypeJunction\Wall\Post::SUBTYPE => hypeJunction\Wall\Post::CLASSNAME
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}