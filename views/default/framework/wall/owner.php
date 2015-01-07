<?php

namespace hypeJunction\Wall;

$owner = elgg_get_page_owner_entity();

if (!$owner) {
	return true;
}

$post = elgg_extract('post', $vars);

$dbprefix = elgg_get_config('dbprefix');
$content = elgg_list_river(array(
	'types' => 'object',
	'subtypes' => get_wall_subtypes(),
	'object_guids' => ($post) ? $post->guid : ELGG_ENTITIES_ANY_VALUE,
	'action_types' => array('create'),
	'joins' => array(
		"LEFT JOIN {$dbprefix}entity_relationships er ON er.guid_two = rv.object_guid",
	),
	'wheres' => array(
		"(rv.subject_guid = $owner->guid OR rv.target_guid = $owner->guid OR (er.guid_one = $owner->guid AND er.relationship = 'tagged_in'))",
	),
	'full_view' => true,
	'limit' => elgg_extract('limit', $vars, 10),
	'list_class' => 'wall-post-list',
	'item_class' => 'wall-post',
	'no_results' => ($post) ? elgg_echo('wall:notfound') : elgg_echo('wall:empty'),
		));

echo $content;

