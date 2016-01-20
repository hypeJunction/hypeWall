<?php

$group_guid = elgg_extract('group_guid', $vars);
$post_guid = elgg_extract('post_guid', $vars);

elgg_entity_gatekeeper($group_guid);
elgg_group_gatekeeper(true, $group_guid);

$group = get_entity($group_guid);
$post = get_entity($post_guid);

elgg_set_page_owner_guid($group->guid);

$name = elgg_instanceof($group, 'object') ? $group->title : $group->name;
$title = elgg_echo('wall:owner', array($name));
elgg_push_breadcrumb($title, hypeWall()->router->normalize('group', $group->guid));

$content = elgg_view("framework/wall/group", array(
	'group' => $group,
	'post' => $post,
		));

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => false,
		));

echo elgg_view_page($title, $layout);
