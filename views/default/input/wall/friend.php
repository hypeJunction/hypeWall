<?php

if (elgg_view_exists('input/tokeninput')) {
	$vars['callback'] = 'elgg_tokeninput_search_friends';

	if (!isset($vars['multiple'])) {
		$vars['multiple'] = true;
	}

	if (!isset($vars['strict'])) {
		$vars['strict'] = true;
	}

	echo elgg_view('input/tokeninput', $vars);
} else {
	echo elgg_view('input/userpicker', $vars);
}