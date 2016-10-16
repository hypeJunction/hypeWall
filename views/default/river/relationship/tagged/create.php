<?php

use hypeJunction\Wall\Post;

$item = elgg_extract('item', $vars);
if (!$item instanceof ElggRiverItem) {
	return;
}

$wall_post = $item->getObjectEntity();
if (!$wall_post instanceof \ElggEntity) {
	return;
}

$view_item = function($wall_post) use ($item) {
	if (!$wall_post instanceof Post) {
		return;
	}
	
	$tagged_user = $item->getSubjectEntity();

	$poster = $wall_post->getOwnerEntity();
	if (!$poster) {
		return;
	}

	$tagged_user_link = elgg_view('output/url', array(
		'text' => $tagged_user->name,
		'href' => $tagged_user->getURL(),
	));

	$poster_link = elgg_view('output/url', array(
		'text' => $poster->name,
		'href' => $poster->getURL(),
	));

	$wall_post_link = elgg_view('output/url', array(
		'text' => elgg_echo('wall:tag:river:post'),
		'href' => $wall_post->getURL(),
	));

	$summary = elgg_echo('wall:tag:river', [$poster_link, $tagged_user_link, $wall_post_link]);

	return elgg_view('river/item', array(
		'item' => $item,
		'summary' => $summary,
		'message' => $wall_post->formatMessage(),
		'attachments' => $wall_post->formatAttachments(),
	));
};

if ($wall_post->getSubtype() == 'wall_tag') {
	// river access is no longer respected, so we are creating a new wall tag object with the appropriate access
	// wall post access id might differ, so we need ignored access
	$ia = elgg_set_ignore_access(true);
	$wall_post = $wall_post->getContainerEntity();
	echo $view_item($wall_post);
	elgg_set_ignore_access($ia);
} else {
	echo $view_item($wall_post);
}

