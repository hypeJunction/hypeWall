<?php

/**
 * Displays a list of attached entities
 * First shows a gallery/grid of images, followed by a list of other entities
 * 
 * @uses $vars['entity'] Entity, whose attachments are being displayed
 */

namespace hypeJunction\Wall;

elgg_push_context('wall-attachments');

$entity = elgg_extract('entity', $vars);

// Show images first
$options = array(
	'relationship' => 'attached',
	'relationship_guid' => $entity->guid,
	'inverse_relationship' => true,
	'metadata_name_value_pairs' => array(
		'name' => 'simpletype', 'value' => 'image',
	),
	'count' => true,
	'limit' => 0,
);
$count = elgg_get_entities_from_relationship($options);

$image_guids = array(ELGG_ENTITIES_NO_VALUE);

if ($count) {
	$class = 'elgg-gallery wall-attachments-gallery';
	if ($count <= 6) {
		if ($count % 2 == 0) {
			$class .= ' wall-block-grid-2';
		} else {
			$class .= ' wall-block-grid-3';
		}
	} else {
		$class .= ' wall-block-grid-4';
	}
	
	unset($options['count']);
	$images = elgg_get_entities_from_relationship($options);

	echo "<ul class=\"$class\">";
	foreach ($images as $image) {
		$image_guids[] = $image->guid;
		echo '<li class="elgg-item">';
		echo elgg_view_entity_icon($image, 'large', array(
			'class' => 'wall-attachment-image-preview',
		));
		echo '</li>';
	}
	echo '</ul>';
}

// List other attachments
$in_image_guids = implode(',', $image_guids);
$options = array(
	'list_class' => 'wall-attachments-list',
	'full_view' => false,
	'relationship' => 'attached',
	'relationship_guid' => $entity->guid,
	'inverse_relationship' => true,
	'wheres' => array("e.guid NOT IN ($in_image_guids)"),
	'limit' => 0,
);
echo elgg_list_entities_from_relationship($options);

elgg_pop_context();
