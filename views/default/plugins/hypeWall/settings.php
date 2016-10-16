<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('wall:settings:url'),
	'name' => 'params[url]',
	'value' => $entity->url,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
]);

if (elgg_is_active_plugin('hypeAttachments')) {
	echo elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('wall:settings:photo'),
		'name' => 'params[photo]',
		'value' => $entity->photo,
		'options_values' => [
			0 => elgg_echo('option:no'),
			1 => elgg_echo('option:yes'),
		],
	]);
}

if (elgg_is_active_plugin('elgg_tokeninput') && elgg_is_active_plugin('hypeAttachments')) {
	echo elgg_view_field([
		'#type' => 'select',
		'#label' => elgg_echo('wall:settings:content'),
		'name' => 'params[content]',
		'value' => $entity->content,
		'options_values' => [
			0 => elgg_echo('option:no'),
			1 => elgg_echo('option:yes'),
		],
	]);
}

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('wall:settings:geopositioning'),
	'name' => 'params[geopositioning]',
	'value' => $entity->geopositioning,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('wall:settings:tag_friends'),
	'name' => 'params[tag_friends]',
	'value' => $entity->tag_friends,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('wall:settings:tags'),
	'name' => 'params[tags]',
	'value' => $entity->tags,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('wall:settings:third_party_wall'),
	'name' => 'params[third_party_wall]',
	'value' => $entity->third_party_wall,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('wall:settings:model:character_limit'),
	'name' => 'params[character_limit]',
	'value' => $entity->character_limit,
]);
