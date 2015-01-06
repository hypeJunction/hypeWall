<?php

/**
 * Form that allows users to update their status
 */

namespace hypeJunction\Wall;

$status = elgg_view('input/wall/status', array(
	'name' => 'status',
	'class' => 'wall-input-status',
	'placeholder' => elgg_echo('wall:status:placeholder')
		));

$url = elgg_view('input/wall/url', array(
	'name' => 'address',
	'placeholder' => elgg_echo('wall:url:placeholder'),
		));

$footer_controls = array();

if (elgg_is_active_plugin('bookmarks')) {
	$footer_controls['bookmark'] = '<label>' . elgg_view('input/checkbox', array(
				'checked' => true,
				'name' => 'make_bookmark',
				'value' => 1
			)) . elgg_echo('wall:make_bookmark') . '</label>';
}

$footer_controls['access'] = elgg_view('input/access', array(
	'class' => 'wall-access',
	'name' => 'access_id'
		));

$footer_controls['submit'] = elgg_view('input/submit', array(
	'value' => elgg_echo('wall:post'),
	'class' => 'elgg-button elgg-button-submit',
		));

$controls = '';
foreach ($footer_controls as $name => $footer_control) {
	$controls .= elgg_format_element('li', array(
		'class' => "wall-bar-control-$name",
			), $footer_control);
}

$footer .= elgg_format_element('ul', array(
	'class' => 'wall-bar-controls',
		), $controls);

$footer = elgg_format_element('fieldset', array(
	'class' => 'elgg-foot text-right',
		), $footer);

$hidden .= elgg_view('input/hidden', array(
	'name' => 'origin',
	'value' => 'wall',
		));

$hidden .= elgg_view('input/hidden', array(
	'name' => 'container_guid',
	'value' => elgg_extract('container_guid', $vars, elgg_get_page_owner_guid())
));

$html = <<<HTML
	<fieldset class="wall-fieldset-status">$status</fieldset>
	<fieldset class="wall-fieldset-attachment">
		<div class="wall-input-url">$url</div>
		<div class="wall-url-preview"></div>
	</fieldset>
	$footer
	$hidden
HTML;

echo $html;
