<?php

$username = elgg_extract('username', $vars);
$owner = get_user_by_username($username);

elgg_entity_gatekeeper($owner->guid, 'user');

elgg_set_page_owner_guid($owner->guid);

$title = elgg_echo('wall:owner', array($owner->name));
elgg_push_breadcrumb($title, hypeWall()->router->normalize(array('owner', $owner->username)));

$post_guid = elgg_extract('post_guid', $vars);
if ($post_guid) {
	elgg_entity_gatekeeper($post_guid);
	$post = get_entity($post_guid);
}

$content = elgg_view("framework/wall/owner", array(
	'post' => $post
		));

if (elgg_is_xhr()) {
	echo $content;
	return;
}

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'content' => $content,
	'filter' => false,
		));
echo elgg_view_page($title, $layout);
