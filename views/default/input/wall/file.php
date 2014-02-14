<?php

if (elgg_view_exists('input/dropzone')) {
	echo elgg_view('input/dropzone', array(
		'name' => 'upload_guids',
		'accept' => "image/*",
		'max' => 25,
		'multiple' => true,
	));
} else {
	echo elgg_view('input/file', array(
		'multiple' => true,
		'name' => 'upload_guids[]',
		'accept' => "image/*",
	));
}