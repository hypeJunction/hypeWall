<?php
/**
 * Displays a list of attached entities
 * First shows a gallery/grid of images, followed by a list of other entities
 * 
 * @uses $vars['entity'] Entity, whose attachments are being displayed
 */
if (!is_callable('hypeapps_get_attachments')) {
	return;
}

$entity = elgg_extract('entity', $vars);

$attachments = hypeapps_get_attachments($entity, [
	'batch' => true,
	'limit' => 0,
		]);

$images = [];
$non_images = [];

foreach ($attachments as $attachment) {
	if ($attachment instanceof ElggFile && $attachment->simpletype == 'image') {
		$images[] = $attachment;
	} else {
		$non_images[] = $attachment;
	}
}

elgg_push_context('wall-attachments');

if (empty($non_images)) {
	echo elgg_view_entity_list($images, [
		'full_view' => false,
		'pagination' => false,
		'list_type' => 'gallery',
		'gallery_class' => 'wall-attachments-gallery',
		'item_view' => 'output/wall/image',
		'rel' => "wall-popup-{$entity->guid}",
	]);
} else {
	echo elgg_view_entity_list(array_merge($images, $non_images), [
		'full_view' => false,
		'pagination' => false,
		'list_class' => 'wall-attachments-list',
	]);
}

elgg_pop_context();
?>
<script>
	require(['output/wall/attachments'], function (lib) {
		lib.init();
	});
</script>