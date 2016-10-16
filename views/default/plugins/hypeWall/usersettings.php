<?php

$user = elgg_extract('user', $vars);

$user_write_access = get_write_access_array();
unset($user_write_access[ACCESS_PUBLIC]);
unset($user_write_access[ACCESS_LOGGED_IN]);

echo elgg_view_field([
	'#type' => 'access',
	'#label' => elgg_echo('wall:usersettings:river_access_id'),
	'#help' => elgg_echo('wall:usersettings:river_access_id:help'),
	'name' => 'params[river_access_id]',
	'value' => elgg_get_plugin_user_setting('river_access_id', $user->guid, 'hypeWall'),
	'options_values' => $user_write_access,
]);

if (elgg_get_plugin_setting('third_party_wall', 'hypeWall')) {
	echo elgg_view_field([
		'#type' => 'access',
		'#label' => elgg_echo('wall:usersettings:third_party_wall'),
		'name' => 'params[third_party_wall]',
		'value' => elgg_get_plugin_user_setting('third_party_wall', $user->guid, 'hypeWall'),
		'options_values' => array(
			0 => elgg_echo('option:no'),
			1 => elgg_echo('option:yes'),
		)
	]);
}