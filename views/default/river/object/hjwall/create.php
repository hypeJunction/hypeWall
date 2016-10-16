<?php

use hypeJunction\Wall\Post;

$item = elgg_extract('item', $vars);
if (!$item instanceof ElggRiverItem) {
	return;
}

$object = $item->getObjectEntity();
if (!$object instanceof Post) {
	return;
}

echo elgg_view('river/item', array(
	'item' => $item,
	'summary' => $object->formatSummary(),
	'message' => $object->formatMessage(),
	'attachments' => $object->formatAttachments(),
));

echo elgg_view('notifier/view_listener', array(
	'entity' => $object,
));