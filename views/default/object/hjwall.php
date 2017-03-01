<?php

use hypeJunction\Wall\Post;

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof Post) {
	return;
}

$poster = $entity->getOwnerEntity();
$wall_owner = $entity->getContainerEntity();

$content = '';

$message = $entity->formatMessage();
if ($message) {
	$content .= elgg_format_element('div', [
		'class' => 'wall-message',
			], $message);
}

$attachments = $entity->formatAttachments();
if ($attachments) {
	$content .= elgg_format_element('div', [
		'class' => 'wall-attachments',
			], $attachments);
}

$menu = '';
if (!elgg_in_context('widgets')) {
	$menu = elgg_view_menu('entity', array(
		'entity' => $entity,
		'handler' => 'wall',
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
	));
}

$subtitle = $entity->formatSummary();
$user_icon = elgg_view_entity_icon($poster, 'medium', array(
	'use_hover' => false,
	'img_class' => 'wall-poster-avatar'
		));

if (elgg_extract('full_view', $vars, false)) {
	$summary = elgg_view('object/elements/summary', [
		'entity' => $entity,
		'title' => false,
		'metadata' => $menu,
		'subtitle' => $subtitle,
	]);

	echo elgg_view('object/elements/full', [
		'entity' => $entity,
		'summary' => $summary,
		'icon' => $icon,
		'body' => $content . elgg_view_comments($entity),
	]);
} else {
	echo elgg_view('object/elements/summary', [
		'entity' => $entity,
		'title' => false,
		'metadata' => $menu,
		'subtitle' => $subtitle,
		'content' => $content,
		'icon' => $user_icon,
	]);
}