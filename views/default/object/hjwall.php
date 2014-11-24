<?php

namespace hypeJunction\Wall;

elgg_load_css('wall');
elgg_load_css('fonts.font-awesome');
elgg_load_css('fonts.open-sans');

$entity = elgg_extract('entity', $vars);
$poster = $entity->getOwnerEntity();
$wall_owner = $entity->getContainerEntity();

$message = format_wall_message($entity);
$content = '<div class="wall-message">' . $message . '</div>';

$attachments = elgg_format_attachments($entity);
if ($attachments) {
	$content .= '<div class="wall-attachments">' . $attachments . '</div>';
}

$menu = elgg_view_menu('entity', array(
	'entity' => $entity,
	'handler' => 'wall',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
		));

if (elgg_in_context('thewire')) {
	$metadata = $menu;
	$menu = '';
}

if (elgg_in_context('widgets')) {
	$menu = $metadata = '';
}

$content .= $menu;

if (elgg_extract('full_view', $vars, false)) {
	$content .= elgg_view_comments($entity);
}

$params = array(
	'entity' => $entity,
	'title' => false,
	'metadata' => $metadata,
	'tags' => false,
	'subtitle' => format_wall_summary($entity),
	'content' => $content,
);

$params = $params + $vars;
$content = '<div class="wall-bubble">' . elgg_view('object/elements/summary', $params) . '</div>';

$user_icon = elgg_view_entity_icon($poster, 'medium', array(
	'use_hover' => false,
	'img_class' => 'wall-poster-avatar'
		));

echo elgg_view_image_block($user_icon, $content, array('class' => 'wall-post'));