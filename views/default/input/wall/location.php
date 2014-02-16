<?php

namespace hypeJunction\Wall;

if (!$vars['value']) {
	$geopositioning = get_geopositioning();
	if ($geopositioning['location']) {
		$vars['value'] = $geopositioning['location'];
	} else if (elgg_instanceof($vars['entity'])) {
		$vars['value'] = $vars['entity']->location;
	} else {
		$vars['value'] = elgg_get_logged_in_user_entity()->location;
	}
}

$vars['callback'] = 'hypeJunction\\Wall\\search_locations';

$vars['class'] = 'wall-location-tokeninput';

if (!isset($vars['multiple'])) {
	$vars['multiple'] = false;
}

if (!isset($vars['strict'])) {
	$vars['strict'] = false;
}

$vars['data-token-delimiter'] = ";";
$vars['data-allow-tab-out'] = true;

echo elgg_view('input/tokeninput', $vars);
echo elgg_view('output/url', array(
	'href' => '#',
	'text' => '<i class="wall-icon wall-icon-find-me"></i>',
	'title' => elgg_echo('wall:tag:location:findme'),
	'class' => 'wall-find-me',
));
