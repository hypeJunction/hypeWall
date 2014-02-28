<?php

$group = elgg_get_page_owner_entity();

if (!elgg_instanceof($group, 'group')) {
	return;
}

echo elgg_list_entities(array(
	'types' => 'object',
	'subtypes' => array('hjwall'),
	'container_guids' => $group->guid,
	'list_class' => 'wall-post-list',
	'full_view' => false,
	'limit' => elgg_extract('limit', $vars, 10),
));

