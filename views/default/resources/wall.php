<?php

$target_guid = elgg_extract('target_guid', $vars);
$post_guids = elgg_extract('post_guids', $vars);

elgg_entity_gatekeeper($target_guid);
elgg_group_gatekeeper(true, $target_guid);

$target = get_entity($target_guid);

elgg_set_page_owner_guid($target->guid);

elgg_push_breadcrumb(elgg_echo('wall'), hypeWall()->router->getPageHandlerId());

if (is_callable(array($target, 'getDisplayName'))) {
	$name = $target->getDisplayName();
} else {
	$name = $target instanceof ElggObject ? $target->title : $target->name;
}
$title = elgg_echo('wall:owner', array($name));
elgg_push_breadcrumb($title, hypeWall()->router->normalize($target->guid));

$content = elgg_view('lists/wall', array(
	'entity' => $target,
	'post_guids' => $post_guids,
		));

if (elgg_is_xhr()) {
	echo $content;
} else {
	$layout = elgg_view_layout('content', array(
		'title' => $title,
		'content' => $content,
		'filter' => false,
	));

	echo elgg_view_page($title, $layout);
}
