<?php

$object = $vars['item']->getObjectEntity();
if (!$object instanceof \hypeJunction\Wall\Post) {
	return;
}

echo elgg_view('river/item', array(
	'item' => $vars['item'],
	'summary' => $object->formatSummary(),
	'message' => $object->formatMessage(),
	'attachments' => $object->formatAttachments(),
));

echo elgg_view('notifier/view_listener', array(
	'entity' => $object,
));