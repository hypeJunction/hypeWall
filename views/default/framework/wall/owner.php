<?php

$owner = elgg_get_page_owner_entity();

if (!$owner) {
	return;
}

$dbprefix = elgg_get_config('dbprefix');
echo elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'hjwall',
	'joins' => array(
		"JOIN {$dbprefix}entity_relationships r ON r.guid_one = $owner->guid AND r.guid_two = e.guid",
	),
	'wheres' => array(
		"r.relationship IN ('wall_owner', 'tagged_in')"
	),
	'list_class' => 'wall-post-list',
));

