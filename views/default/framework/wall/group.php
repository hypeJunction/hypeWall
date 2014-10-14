<?php

namespace hypeJunction\Wall;

$group = elgg_get_page_owner_entity();

if (!elgg_instanceof($group, 'group')) {
	return true;
}

$post = elgg_extract('post', $vars);

$content = elgg_list_river(array(
	'types' => 'object',
	'subtypes' => get_wall_subtypes(),
	'object_guids' => ($post) ? $post->guid : ELGG_ENTITIES_ANY_VALUE,
	'target_guids' => $group->guid,
	'full_view' => true,
	'limit' => elgg_extract('limit', $vars, 10),
	'list_class' => 'wall-post-list wall-group-post-list',
	'item_class' => 'wall-post',
	'no_results' => ($post) ? elgg_echo('wall:notfound') : elgg_echo('wall:empty'),
		));

echo $content;

