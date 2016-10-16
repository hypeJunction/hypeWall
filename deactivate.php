<?php

use hypeJunction\Wall\Post;

$subtypes = array(
	Post::SUBTYPE,
);

foreach ($subtypes as $subtype => $class) {
	update_subtype('object', $subtype);
}