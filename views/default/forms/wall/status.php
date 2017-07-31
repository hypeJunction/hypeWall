<?php

elgg_register_menu_item('wall-filter', array(
	'name' => 'status',
	'text' => elgg_view_icon('comment', ['class' => 'wall-icon wall-icon-status']),
	'title' => elgg_echo('wall:status'),
	'href' => '#wall-form-status',
	'link_class' => 'wall-tab',
	'selected' => true,
	'priority' => 100
));

$entity = elgg_extract('entity', $vars);

$tools = [];
$fields = [];

$fields[] = [
	'#type' => 'wall/status',
	'#class' => 'wall-field-status',
	'class' => 'wall-input-status',
	'name' => 'status',
	'value' => $entity ? $entity->description : get_input('status'),
	'placeholder' => elgg_echo('wall:status:placeholder'),
	'rows' => 2,
	'entity' => $entity,
];

if (elgg_get_plugin_setting('url', 'hypeWall')) {

	$value = $entity ? $entity->address : get_input('address');
	$fields[] = [
		'#type' => 'wall/url',
		'#label' => elgg_echo('wall:url'),
		'#class' => ['wall-field-url', !$value ? 'hidden' : ''],
		'name' => 'address',
		'value' => $value,
		'class' => 'wall-url',
		'placeholder' => elgg_echo('wall:url:placeholder'),
		'entity' => $entity,
	];

	$tools[] = [
		'name' => 'add-url',
		'text' => elgg_view_icon('link'),
		'title' => elgg_echo('wall:url'),
		'data-section' => '.wall-field-url',
		'item_class' => $value ? 'hidden' : '',
	];
}

if (elgg_get_plugin_setting('photo', 'hypeWall') && elgg_is_active_plugin('hypeAttachments')) {

	$fields[] = [
		'#type' => 'wall/file',
		'#label' => elgg_echo('wall:photo'),
		'#class' => 'wall-field-photo hidden',
		'name' => 'upload_guids',
		'class' => 'wall-photo',
		'entity' => $entity,
	];

	$tools[] = [
		'name' => 'add-photo',
		'text' => elgg_view_icon('camera'),
		'title' => elgg_echo('wall:photo'),
		'data-section' => '.wall-field-photo',
	];
}

if (elgg_get_plugin_setting('content', 'hypeWall') && elgg_is_active_plugin('hypeAttachments')) {
	$fields[] = [
		'#type' => 'wall/attachment',
		'#label' => elgg_echo('wall:attachment'),
		'#class' => 'wall-field-content hidden',
		'name' => 'attachment_guids',
		'class' => 'wall-content',
		'entity' => $entity,
	];

	$tools[] = [
		'name' => 'add-content',
		'text' => elgg_view_icon('clipboard'),
		'title' => elgg_echo('wall:attachment'),
		'data-section' => '.wall-field-content',
	];
}

if (elgg_get_plugin_setting('geopositioning', 'hypeWall')) {
	$value = $entity ? $entity->location : get_input('location');
	$find_me = elgg_view('output/url', [
		'href' => '#',
		'text' => elgg_echo('wall:find_me'),
		'title' => elgg_echo('wall:tag:location:findme'),
		'class' => 'wall-find-me',
		'entity' => $entity,
	]);

	$fields[] = [
		'#type' => 'location',
		'#label' => $find_me . elgg_echo('wall:location'),
		'#class' => ['wall-field-location', !$value ? 'hidden' : ''],
		'class' => 'wall-input-location',
		'name' => 'location',
		'value' => $value,
		'placeholder' => elgg_echo('wall:tag:location:hint'),
		'entity' => $entity,
	];

	$tools[] = [
		'name' => 'add-location',
		'text' => elgg_view_icon('map-marker'),
		'title' => elgg_echo('wall:location'),
		'data-section' => '.wall-field-location',
		'item_class' => $value ? 'hidden' : '',
	];
}

if (elgg_get_plugin_setting('tag_friends', 'hypeWall')) {
	$fields[] = [
		'#type' => 'wall/friend',
		'#label' => elgg_echo('wall:tag_friends'),
		'#class' => 'wall-field-friends hidden',
		'name' => 'friend_guids',
		'data-hint-text' => elgg_echo('wall:tag:friends:hint'),
		'entity' => $entity,
	];
	$tools[] = [
		'name' => 'tag-friends',
		'text' => elgg_view_icon('user-plus'),
		'title' => elgg_echo('wall:tag_friends'),
		'data-section' => '.wall-field-friends',
	];
}

if (elgg_get_plugin_setting('tags', 'hypeWall')) {
	$value = $entity ? $entity->tags : get_input('tags');
	$fields[] = [
		'#type' => 'tags',
		'#label' => elgg_echo('wall:tags'),
		'#class' => ['wall-field-tags', !$value ? 'hidden' : ''],
		'name' => 'tags',
		'value' => $value,
		'class' => 'wall-tags',
		'entity' => $entity,
	];

	$tools[] = [
		'name' => 'add-tags',
		'text' => elgg_view_icon('tags'),
		'title' => elgg_echo('wall:tags'),
		'data-section' => '.wall-field-tags',
		'item_class' => $value ? 'hidden' : '',
	];
}

$fields[] = [
	'#type' => 'hidden',
	'name' => 'origin',
	'value' => 'wall',
];

$fields[] = [
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
];

$fields[] = [
	'#type' => 'hidden',
	'name' => 'container_guid',
	'value' => $entity ? $entity->container_guid : elgg_extract('container_guid', $vars, elgg_get_page_owner_guid()),
];

foreach ($fields as $field) {
	echo elgg_view_field($field);
}

echo elgg_view('output/attachments', [
	'entity' => $entity,
]);

$footer = elgg_view_menu('wall-tools', [
	'class' => 'elgg-menu-hz float',
	'sort_by' => 'priority',
	'items' => $tools,
	'entity' => $entity,
		]);

$footer .= elgg_view_field([
	'#type' => 'fieldset',
	'align' => 'horizontal',
	'justify' => 'right',
	'fields' => [
		[
			'#type' => 'access',
			'name' => 'access_id',
			'value' => $entity ? $entity->access_id : get_default_access(),
			'class' => 'wall-access',
		],
		[
			'#type' => 'submit',
			'value' => elgg_echo('wall:post'),
		],
	],
		]);

elgg_set_form_footer($footer);


