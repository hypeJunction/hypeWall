<?php

$char_limit = elgg_get_plugin_setting('character_limit', 'hypeWall', 0);
if ($char_limit > 0) {
	$vars['data-limit'] = $char_limit;

	$indicator = elgg_format_element('span', [
		'data-counter-indicator' => '',
		'class' => 'wall-chars-counter',
	], $char_limit);

	$indicator .= elgg_echo('wall:characters_remaining');

	$counter = elgg_format_element('div', [
		'class' => 'wall-status-counter',
		'data-counter' => '',
	], $indicator);

}

echo elgg_view('input/plaintext', $vars);
echo $counter;
