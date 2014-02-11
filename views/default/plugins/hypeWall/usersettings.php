<?php

$entity = elgg_extract('entity', $vars);

echo '<div>';
echo '<label>' . elgg_echo('wall:usersettings:river_access_id') . '</label>';
echo '<div class="elgg-text-help">' . elgg_echo('wall:usersettings:river_access_id:help') . '</div>';

$user_write_access = get_write_access_array();
unset($user_write_access[ACCESS_PUBLIC]);
unset($user_write_access[ACCESS_LOGGED_IN]);

echo elgg_view('input/access', array(
	'name' => 'params[river_access_id]',
	'value' => $entity->river_access_id,
	'options_values' => $user_write_access,
));
echo '</div>';