<?php

function hj_wall_get_tags_str($object) {

	$tags = elgg_get_entities_from_relationship(array(
		'type' => 'user',
		'relationship' => 'tagged_in',
		'relationship_guid' => $object->guid,
		'inverse_relationship' => true,
		'limit' => 0
			));

	if ($tags) {
		foreach ($tags as $tag) {
			$tagged_users[] = elgg_view('output/url', array(
				'text' => $tag->name,
				'href' => $tag->getURL()
					));
		}
		$tagged_str = implode(', ', $tagged_users);
		$tagged_str = elgg_echo('hj:wall:with', array($tagged_str));
	}

	if ($object->location) {
		$location_str = elgg_echo('hj:wall:at', array($object->location));
	}

	if ($tags || $object->location) {
		$extras = "<span class=\"hj-wall-river-extras\">  -$tagged_str$location_str</span>";
	}

	return $extras;
}