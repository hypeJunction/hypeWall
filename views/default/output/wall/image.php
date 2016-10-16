<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggFile) {
	return;
}

$image = elgg_view('output/img', [
	'src' => $entity->getIconUrl('large'),
	'alt' => $entity->getDisplayName(),
]);

foreach (['master', 'original', 'large'] as $size) {
	if ($entity->hasIcon($size)) {
		$href = $entity->getIconURL($size);
		break;
	}
}

echo elgg_view('output/url', [
	'href' => $href,
	'text' => $image,
	'rel' => elgg_extract('rel', $vars),
	'class' => 'wall-popup-link',
]);
