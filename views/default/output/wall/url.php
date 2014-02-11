<?php

namespace hypeJunction\Wall;

$value = elgg_extract('value', $vars);

if (!$value || !filter_var($value, FILTER_VALIDATE_URL) || !($fp = curl_init($value))) {
	return;
}

if (elgg_view_exists('output/embed')) {
	echo elgg_view('output/embed', $vars);
} else {
	echo elgg_view('output/url', array(
		'href' => $vars['value'],
		'text' => $vars['value'],
		'title' => 'oembed',
		'target' => '_blank'
	));
}