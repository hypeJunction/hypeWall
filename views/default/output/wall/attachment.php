<?php

namespace hypeJunction\Wall;

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity)) {
	return;
}

if (elgg_view_exists('embed/item/entity')) {
	echo elgg_view('embed/item/entity', $vars);
} else {
	echo elgg_view('output/url', array(
		'href' => $entity->getURL(),
		'text' => $entity->title,
	));
}
