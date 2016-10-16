<?php

if (sizeof($vars['menu']['default']) <= 1) {
	return;
}

echo elgg_view('navigation/menu/default', $vars);