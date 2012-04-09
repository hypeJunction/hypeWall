<?php

elgg_load_js('hj.wall.base');

elgg_load_css('hj.wall.base');
elgg_load_css('jquery.oembed');
elgg_load_css('hj.framework.jquitheme');

if (!elgg_is_logged_in()) {
	return true;
}

//if (!$items = elgg_get_plugin_setting('items', 'hypeWall')) {
	$items = 'status,photo,file';
	elgg_set_plugin_setting('items', $items, 'hypeWall');
//}
$items = explode(',', $items);

foreach($items as $item) {
	if ($item == 'status' && elgg_get_page_owner_guid() !== elgg_get_logged_in_user_guid()) {
		$item = 'post';
	}
	$tabs[] = array(
		'text' => elgg_view_icon("wall-$item") . '<span class="elgg-icon-text">' . elgg_echo("hj:wall:$item") . '</span>',
		'href' => "#hj-wall-form-$item",
		'class' => 'hj-wall-item-link'
	);

	$form_class = "hj-wall-form hj-wall-form-ajax";

	$forms .= elgg_view_form("wall/$item", array(
		'enctype' => 'multipart/form-data',
		'class' => $form_class,
		'id' => "hj-wall-form-$item",
	));

}

$tabs = elgg_view('navigation/tabs', array(
	'id' => 'hj-wall-item-tabs',
	'class' => 'hj-wall-item-tabs',
	'tabs' => $tabs,
	'type' => 'horizontal'
));

$ajax_holder = '<div class="hj-ajax-loader loader-bar hj-wall-ajax-loader hj-right hidden"></div>';

$html = <<<HTML
<div id="hj-wall">
	$tabs$ajax_holder
	<div class="clear"></div>
	$forms
</div>
HTML;

echo $html;