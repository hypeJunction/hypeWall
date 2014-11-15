<?php

$entity = elgg_extract('entity', $vars);

$options = array(
	'list_type' => 'gallery',
	'gallery_class' => 'wall-attachments-gallery',
	'full_view' => false,
	'limit' => 0,
	'relationship' => 'attached',
	'relationship_guid' => $entity->guid,
	'inverse_relationship' => true,
	'count' => true,
);

echo elgg_list_entities_from_relationship($options);
