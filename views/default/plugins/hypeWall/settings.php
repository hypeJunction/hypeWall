<?php

namespace hypeJunction\Wall;

$entity = elgg_extract('entity', $vars);

echo '<h3>' . elgg_echo('wall:settings:form') . '</h3>';

echo '<div>';
echo '<label>' . elgg_echo('wall:settings:status') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[status]',
	'value' => $entity->status,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('wall:settings:url') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[url]',
	'value' => $entity->url,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('wall:settings:photo') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[photo]',
	'value' => $entity->photo,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	)
));
echo '</div>';
//
//echo '<div>';
//echo '<label>' . elgg_echo('wall:settings:file') . '</label>';
//echo elgg_view('input/dropdown', array(
//	'name' => 'params[file]',
//	'value' => $entity->file,
//	'options_values' => array(
//		0 => elgg_echo('option:no'),
//		1 => elgg_echo('option:yes'),
//	)
//));
//echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('wall:settings:content') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[content]',
	'value' => $entity->content,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	)
));
echo '</div>';

echo '<h3>' . elgg_echo('wall:settings:features') . '</h3>';

echo '<div>';
echo '<label>' . elgg_echo('wall:settings:geopositioning') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[geopositioning]',
	'value' => $entity->geopositioning,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('wall:settings:tag_friends') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'params[tag_friends]',
	'value' => $entity->tag_friends,
	'options_values' => array(
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	)
));
echo '</div>';