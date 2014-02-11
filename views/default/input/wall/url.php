<?php

if (isset($vars['class'])) {
	$vars['class'] = "{$vars['class']} wall-url";
} else {
	$vars['class'] = 'wall-url';
}

echo elgg_view('input/url', $vars);