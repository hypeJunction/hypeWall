<?php

namespace hypeJunction\Wall;

$plugin_id = basename(__DIR__);

$settings = array(
	'model' => 'hjwall',
	'status' => true,
	'url' => true,
	'photo' => true,
	'content' => true,
	'default_form' => 'status',
	'geopositioning' => true,
	'tag_friends' => true,
	'third_party_wall' => false,
	'status_input_type' => 'plaintext',
);

foreach ($settings as $name => $default_value) {
	if (is_null(elgg_get_plugin_setting($name, $plugin_id))) {
		elgg_set_plugin_setting($name, $default_value, $plugin_id);
	}
}