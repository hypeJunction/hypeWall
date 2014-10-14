<?php

/**
 * Add tagged friends and embeddable content to wire posts
 */

namespace hypeJunction\Wall;

$entity = elgg_extract('entity', $vars);

$tagged_friends = get_tagged_friends($entity, 'links');
if ($tagged_friends) {
	echo '<span class="elgg-subtext wall-tagged-friends">' . elgg_echo('wall:with', array(implode(', ', $tagged_friends))) . '</span>';
}

$attachments = format_wall_attachments($entity);
if ($attachments) {
	echo $attachments;
}