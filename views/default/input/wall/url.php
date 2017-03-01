<?php

echo elgg_view('input/url', $vars);

if (elgg_is_active_plugin('bookmarks')) {
	echo elgg_view('input/checkbox', [
		'checked' => false,
		'name' => 'make_bookmark',
		'value' => 1,
		'label' => elgg_echo('wall:make_bookmark'),
		'class' => 'wall-make-bookmark-checkbox',
	]);
}

$preview = '';
$value = elgg_extract('value', $vars);
if ($value) {
	$preview = elgg_view('output/wall/url', [
		'value' => $value,
	]);
}

echo elgg_format_element('div', [
	'class' => 'wall-url-preview',
], $preview);
