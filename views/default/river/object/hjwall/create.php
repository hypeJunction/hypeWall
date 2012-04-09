<?php

$subject = $vars['item']->getSubjectEntity();
$object = $vars['item']->getObjectEntity();

$extras = hj_wall_get_tags_str($object);

$wall_owner = elgg_get_entities_from_relationship(array(
	'type' => 'user',
	'relationship' => 'wall_owner',
	'relationship_guid' => $object->guid,
	'inverse_relationship' => true
));

$wall_owner = $wall_owner[0];

if (elgg_instanceof($wall_owner) && $wall_owner->guid != $subject->guid && $wall_owner->guid != elgg_get_page_owner_guid()) {
	$by = elgg_view('output/url', array(
		'text' => $subject->name,
		'href' => $subject->getURL()
	));
	$on = elgg_view('output/url', array(
		'text' => $wall_owner->name,
		'href' => $wall_owner->getURL()
	));
	$prefix = elgg_echo('hj:wall:new:wall:post', array($by, $on));
}

$summary = parse_urls($prefix . $object->description . $extras);

if (!$attachment = get_entity($object->attachment)) {
	$att_str = '<div class="elgg-content">' . $object->attachment . '</div>';
} else {
	$att_str = '<div class="elgg-content">' . elgg_view_entity($attachment, array(
		'full_view' => false,
		'icon_only' => true,
		'icon_size' => 'master'
	));
}

echo elgg_view('river/item', array(
	'item' => $vars['item'],
	'summary' => $summary,
	//'message' => $excerpt,
    'attachments' => $att_str
));
