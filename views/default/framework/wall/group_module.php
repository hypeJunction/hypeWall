<?php

namespace hypeJunction\Wall;

$group = elgg_get_page_owner_entity();

if ($group->wall_enable !== "yes") {
	return true;
}

elgg_push_context('widgets');
$content = elgg_list_river(array(
	'types' => 'object',
	'subtypes' => get_wall_subtypes(),
	'target_guids' => $group->guid,
	'limit' => elgg_extract('limit', $vars, 10),
	'list_class' => 'wall-post-list wall-widget-list',
	'no_results' => elgg_echo('wall:empty'),
	'full_view' => true,
	'pagination' => false,
		));
elgg_pop_context();

if (!$content) {
	return;
}

$all_link = elgg_view('output/url', array(
	'href' => "wall/group/$group->guid",
	'text' => elgg_echo('link:view:all'),
	'is_trusted' => true,
		));

$new_link = elgg_view('output/url', array(
	'href' => "wall/group/$group->guid",
	'text' => elgg_echo('wall:groups:post'),
	'is_trusted' => true,
		));

echo elgg_view('groups/profile/module', array(
	'title' => elgg_echo('wall:groups'),
	'content' => $content,
	'all_link' => $all_link,
	'add_link' => $new_link,
));
