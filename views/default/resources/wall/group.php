<?php

/**
 * @deprecated since 4.4
 */
$group_guid = elgg_extract('group_guid', $vars);
$post_guid = elgg_extract('post_guid', $vars);

echo elgg_view('resources/wall', array(
	'target_guid' => $group_guid,
	'post_guids' => array($post_guid),
));