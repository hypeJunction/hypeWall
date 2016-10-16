<?php

$name = elgg_extract('name', $vars);
if (elgg_view_exists('input/dropzone')) {
	echo elgg_view('input/dropzone', [
		'accept' => "image/*",
		'max' => 25,
		'multiple' => true,
		'action' => elgg_normalize_url('action/dropzone/upload'),
	] + $vars);
} else {
	$vars['name'] = "{$name}[]";
	echo elgg_view('input/file', [
		'multiple' => true,
		'accept' => "image/*",
	] + $vars);
}