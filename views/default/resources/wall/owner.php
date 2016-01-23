<?php

$username = elgg_extract('username', $vars);
$owner = get_user_by_username($username);

$post_guid = elgg_extract('post_guid', $vars);

echo elgg_view('resources/wall', array(
	'target_guid' => $user->guid,
	'post_guids' => array($post_guid),
));