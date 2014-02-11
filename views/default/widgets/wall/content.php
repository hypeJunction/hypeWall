<?php

elgg_push_context('wall');
$content = elgg_view("framework/wall/container", array('size' => 'small'));
$content .= elgg_view("framework/wall/owner", array(
	'limit' => $vars['entity']->num_display
));
elgg_pop_context();

echo $content;

$wall_url = "wall/owner/" . elgg_get_page_owner_entity()->username;
$wall_link = elgg_view('output/url', array(
	'href' => $wall_url,
	'text' => elgg_echo('wall:moreposts'),
	'is_trusted' => true,
		));
echo "<span class=\"elgg-widget-more\">$wall_link</span>";
