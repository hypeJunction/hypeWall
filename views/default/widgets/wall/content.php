<?php

elgg_push_context('wall');

$entity = elgg_extract('entity', $vars);


if ($entity->show_add_form) {
	$content = elgg_view("framework/wall/container");
}

$owner = elgg_get_page_owner_entity();
if (!$owner) {
	$owner = $entity->getOwnerEntity();
}

$pagination = elgg_is_active_plugin('hypeLists');
$content .= elgg_view('lists/wall', array(
	'entity' => $owner,
	'options' => array(
		'limit' => $entity->num_display,
		'list_class' => 'wall-post-list wall-widget-list',
		'pagination' => $pagination,
		'pagination_type' => 'infinite',
	),
		));

if (!$pagination) {
	$wall_link = elgg_view('output/url', array(
		'href' => "wall/$owner->guid",
		'text' => elgg_echo('wall:moreposts'),
		'is_trusted' => true,
	));

	$content .= elgg_format_element('span', array('class' => 'elgg-widget-more'), $wall_link);
}

echo $content;

elgg_pop_context();
