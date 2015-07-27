<?php

$input_type = hypeWall()->config->status_input_type;
if (!$input_type) {
	$input_type = 'plaintext';
}

if (!$vars['value'] && elgg_instanceof($vars['entity'])) {
	$vars['value'] = $vars['entity']->description;
}

$vars['class'] = "{$vars['class']} wall-input-status-wire";
$char_limit = hypeWall()->config->character_limit;
if ($char_limit > 0) {
	$vars['data-limit'] = $char_limit;
	$counter = '<div class="wall-status-counter" data-counter>';
	$counter .= '<span data-counter-indicator class="wall-chars-counter">';
	$counter .= $char_limit;
	$counter .= '</span>';
	$counter .= elgg_echo('wall:characters_remaining');
	$counter .= '</div>';
}

if (!isset($vars['name'])) {
	$vars['name'] = 'status';
}

echo $counter;
echo elgg_view("input/$input_type", $vars);
