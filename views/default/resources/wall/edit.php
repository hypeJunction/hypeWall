<?php

use hypeJunction\Wall\Post;

$guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', Post::SUBTYPE);

$entity = get_entity($guid);
if (!$entity->canEdit()) {
	forward('', '403');
}

$container = $entity->getContainerEntity();
if ($container) {
	$title = elgg_echo('wall:owner', [$container->getDisplayName()]);
	elgg_push_breadcrumb($title, "wall/$container->guid");
	elgg_set_page_owner_guid($container->guid);
}

$title = elgg_echo('wall:edit');
$content = elgg_view('framework/wall/container', [
	'entity' => $entity,
]);

$layout = elgg_view_layout('one_sidebar', [
	'title' => $title,
	'content' => $content,
]);

echo elgg_view_page($title, $layout);

