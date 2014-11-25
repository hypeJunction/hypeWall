<?php

namespace hypeJunction\Wall;

elgg_load_css('wall');
elgg_load_css('fonts.font-awesome');
elgg_load_css('fonts.open-sans');

elgg_push_context('wall');

if ($vars['entity']->show_add_form) {
	$content = elgg_view("framework/wall/container");
}

$owner = elgg_get_page_owner_entity();
if (!$owner) {
	if (!elgg_is_logged_in()) {
		return true;
	}
	$owner = elgg_get_logged_in_user_entity();
}

$dbprefix = elgg_get_config('dbprefix');
$content .= elgg_list_river(array(
	'types' => 'object',
	'subtypes' => get_wall_subtypes(),
	'target_guids' => $owner->guid,
	'limit' => $vars['entity']->num_display,
	'list_class' => 'wall-post-list wall-widget-list',
	'no_results' => elgg_echo('wall:empty'),
	'full_view' => true,
	'pagination' => false,
		));

elgg_pop_context();

echo $content;

if (elgg_instanceof($owner, 'user')) {
	$wall_url = "wall/owner/" . elgg_get_page_owner_entity()->username;
} else if (elgg_instanceof($owner, 'group')) {
	$wall_url = "wall/group/" . $owner->guid;
} else {
	$wall_url = "wall/container/" . $owner->guid;
}

$wall_link = elgg_view('output/url', array(
	'href' => $wall_url,
	'text' => elgg_echo('wall:moreposts'),
	'is_trusted' => true,
		));
echo "<span class=\"elgg-widget-more\">$wall_link</span>";
