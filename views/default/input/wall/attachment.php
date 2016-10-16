<?php

if (!elgg_is_active_plugin('elgg_tokeninput')) {
	return;
}

$vars['callback'] = 'elgg_tokeninput_search_owned_entities';
$vars['data-results-limit'] = 10;

$vars['class'] = 'wall-attachment-tokeninput';

if (!isset($vars['multiple'])) {
	$vars['multiple'] = true;
}

if (!isset($vars['strict'])) {
	$vars['strict'] = true;
}

echo elgg_view('input/tokeninput', $vars);