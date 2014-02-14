<?php

namespace hypeJunction\Wall;

$entity = elgg_extract('entity', $vars);
$poster = $entity->getOwnerEntity();
$wall_owner = $entity->getContainerEntity();

if ($wall_owner && $wall_owner->guid != $poster->guid && $wall_owner->guid != elgg_get_page_owner_guid()) {
	$by = elgg_view('output/url', array(
		'text' => $poster->name,
		'href' => $poster->getURL()
	));
	$on = elgg_view('output/url', array(
		'text' => $wall_owner->name,
		'href' => $wall_owner->getURL()
	));
	$summary = elgg_echo('wall:new:wall:post', array($by, $on));
}

$message = format_wall_message($entity);

if ($entity->address) {
	$att_str = elgg_view('output/wall/url', array(
		'value' => $entity->address,
	));
}
$att_str .= $entity->html;
$attachments = elgg_get_entities_from_relationship(array(
	'relationship' => 'attached',
	'relationship_guid' => $entity->guid,
	'inverse_relationship' => true,
	'limit' => false,
		));
if ($attachments) {
	if (count($attachments) > 1) {
		$att_str .= elgg_view_entity_list($attachments, array(
			'list_type' => 'gallery',
			'full_view' => false,
			'size' => 'medium'
		));
	} else {
		foreach ($attachments as $attachment) {
			$att_str .= elgg_view('output/wall/attachment', array(
				'entity' => $attachment
			));
		}
	}
}

if ($vars['full_view']) {
	$body .= elgg_view_comments($entity);
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $entity,
	'handler' => 'wall',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
		));

if (elgg_in_context('widgets')) {
	$metadata = '';
}

$params = array(
	'entity' => $entity,
	'title' => $summary,
	'tags' => false,
	'metadata' => $metadata,
	'subtitle' => false,
	'content' => $message .$att_str,
);

$params = $params + $vars;
$content = elgg_view('object/elements/summary', $params);

$user_icon = elgg_view_entity_icon($poster, 'small', array(
	'use_hover' => false,
	'img_class' => 'wall-poster-avatar'
		));

if ($poster->guid == elgg_get_page_owner_guid()) {
	echo elgg_view_image_block('', $content, array(
		'image_alt' => $user_icon,
		'class' => 'wall-post-alt'
	));
} else {
	echo elgg_view_image_block($user_icon, $content, array('class' => 'wall-post'));
}

