<?php

namespace hypeJunction\Wall;

$object = $vars['item']->getObjectEntity();

echo elgg_view('river/item', array(
	'item' => $vars['item'],
	'summary' => format_wall_summary($object),
	'message' => format_wall_message($object),
	'attachments' => format_wall_attachments($object),
));
