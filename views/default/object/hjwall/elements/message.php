<?php

use hypeJunction\Wall\Post;

/**
 * Outputs formatted wall message
 *
 * @uses $vars['entity']          Wall post
 * @uses $vars['include_address'] Include attached URL address
 */

$entity = elgg_extract('entity', $vars);

if (!$entity instanceof Post) {
	return true;
}

$status = elgg_view('output/longtext', [
	'value' => $entity->description,
	'class' => 'wall-status',
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

$tagged_friends = $entity->getTaggedFriends('links');
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
