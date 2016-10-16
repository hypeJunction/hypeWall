<?php

$guid = get_input('guid');
$post = get_entity($guid);

$user = elgg_get_logged_in_user_entity();

if (!$post) {
	return elgg_error_response(elgg_echo('wall:error:not_found'));
}

$relationship = check_entity_relationship($user->guid, 'tagged_in', $post->guid);
if (!$relationship instanceof \ElggRelationship) {
	return elgg_error_response(elgg_echo('wall:remove_tag:error'));
}

$id = $relationship->id;
$relationship->delete();

elgg_delete_river(array(
	'subject_guids' => $user->guid,
	'object_guids' => $post->guid,
	'action_types' => 'tagged',
));

$friend_wall_tags = elgg_get_entities([
	'types' => 'object',
	'subtypes' => 'wall_tag',
	'owner_guids' => $user->guid,
	'container_guids' => $post->guid,
]);

if ($friend_wall_tags) {
	foreach ($friend_wall_tags as $tag) {
		// This will remove river entries as well
		$tag->delete();
	}
}

return elgg_ok_response('', elgg_echo('wall:remove_tag:success'));
