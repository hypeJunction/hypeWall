<?php

/**
 * @deprecated since 4.4
 */

$owner = elgg_get_page_owner_entity();

$post_guids = array();
$post = elgg_extract('post', $vars);
if ($post instanceof ElggEntity) {
	$post_guids = array($post->guid);
}

echo elgg_view('lists/wall', array(
	'entity' => $owner,
	'post_guids' => $post_guids,
	'list_class' => 'wall-group-post-list',
	'limit' => elgg_extract('limit', $vars, elgg_get_config('default_limit')) ? : 10,
));