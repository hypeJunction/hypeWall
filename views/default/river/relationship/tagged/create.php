<?php

/**
 * Wall post tag river item
 */

namespace hypeJunction\Wall;

elgg_push_context('wall');

// River access level will vary from that of the original post
$ia = elgg_set_ignore_access(true);
$tagged_user = $vars['item']->getSubjectEntity();
$wall_post = $vars['item']->getObjectEntity();
$poster = $wall_post->getOwnerEntity();

$tagged_user_link = elgg_view('output/url', array(
	'text' => $tagged_user->name,
	'href' => $tagged_user->getURL()
		));

$poster_link = elgg_view('output/url', array(
	'text' => $poster->name,
	'href' => $poster->getURL()
		));
$summary = elgg_echo('wall:tag:river', array($poster_link, $tagged_user_link));

$attachment = elgg_view_entity($wall_post, array(
	'full_view' => false,
		));

elgg_set_ignore_access($ia);

echo elgg_view('river/item', array(
	'item' => $vars['item'],
	'summary' => $summary,
	'message' => $message,
	'attachments' => $attachment
));

elgg_pop_context();
