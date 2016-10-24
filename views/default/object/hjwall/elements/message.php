<?php

/**
 * Outputs formatted wall message
 * @uses $vars['entity'] Entity (wall or wire post, or other?)
 * @uses $vars['include_address'] Include attached URL address
 */

namespace hypeJunction\Wall;

$entity = elgg_extract('entity', $vars);
/* @var ElggEntity $entity */

if (!elgg_instanceof($entity)) {
	return true;
}

$status = elgg_view('output/longtext', [
	'value' => $entity->description,
]);

if (elgg_view_exists('output/linkify')) {
	$status = elgg_view('output/linkify', array(
		'value' => $status,
	));
}

$message = array($status);

$address = $entity->address;
if ($address) {
	$include_address = elgg_extract('include_address', $vars, (strpos($status, $address) === false)) || !$status;
	if ($include_address) {
		$message[] = elgg_view('output/url', array(
			'href' => $address,
			'class' => 'wall-attached-url',
		));
	}
}

$tagged_friends = get_tagged_friends($entity, 'links');
if ($tagged_friends) {
	$message[] = elgg_format_element('span', array(
		'class' => 'wall-tagged-friends',
			), elgg_echo('wall:with', array(implode(', ', $tagged_friends))));
}

$location = $entity->getLocation();
if ($location) {
	$location = elgg_view('output/wall/location', array(
		'value' => $location
	));
	$message[] = elgg_format_element('span', array(
		'class' => 'wall-tagged-location'
			), elgg_echo('wall:at', array($location)));
}

echo implode(' ', $message);
