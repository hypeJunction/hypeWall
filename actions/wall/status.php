<?php

use hypeJunction\Wall\Post;

$poster = elgg_get_logged_in_user_entity();

$guid = get_input('guid');
$status = get_input('status', '');
$title = htmlentities(get_input('title', ''), ENT_QUOTES, 'UTF-8');
$location = get_input('location');
$access_id = get_input('access_id', get_default_access());
$address = get_input('address');
$upload_guids = (array) get_input('upload_guids', []);

$friend_guids = get_input('friend_guids', '');
if (!is_array($friend_guids)) {
	$friend_guids = string_to_tag_array((string) $friend_guids);
}

$attachment_guids = get_input('attachment_guids', '');
if (!is_array($attachment_guids)) {
	$attachment_guids = string_to_tag_array((string) $attachment_guids);
}

$tags = get_input('tags', '');
if (!is_array($tags)) {
	$tags = string_to_tag_array((string) $tags);
}

if (is_callable('hypeapps_extract_tokens')) {
	$tokens = hypeapps_extract_tokens($status);
	
	$tags = array_unique(array_merge($tags, $tokens['hashtags']));
	
	foreach ($tokens['usernames'] as $username) {
		$user = get_user_by_username($username);
		$friend_guids[] = $user->guid;
	}
	
	if (empty($address) && !empty($tokens['urls'])) {
		$address = array_shift($tokens['urls']);
	}
}

$tags = array_map(function($tag) {
	$tag = trim($tag);
	if (strpos($tag, '#') === 0) {
		$tag = substr($tag, 1);
	}
	return $tag;
}, $tags);

if (empty($status) && empty($address) && empty($attachment_guids) && empty($upload_guids)) {
	return elgg_error_response(elgg_echo('wall:error:empty_form'));
}

if ($guid) {
	$action = 'update';
	$post = get_entity($guid);
	if (!$post) {
		return elgg_error_response(elgg_echo('wall:error:not_found'));
	}
	$container = $post->getContainerEntity();
} else {
	$action = 'create';
	$container_guid = get_input('container_guid');
	if ($container_guid) {
		$container = get_entity($container_guid);
	} else {
		$container = $poster;
	}

	if (!$container || !$container->canWriteToContainer($poster->guid, 'object', Post::SUBTYPE)) {
		return elgg_error_response(elgg_echo('wall:error:container_permissions'));
	}

	$post = new Post();
	$post->owner_guid = $poster->guid;
	$post->container_guid = $container->guid;
}

$post->title = $title;
$post->description = $status;
$post->access_id = $access_id;
$post->origin = 'wall';
$post->tags = $tags;
$post->setLocation($location);
$post->address = $address;

if (!$post->save()) {
	return elgg_error_response(elgg_echo('wall:create:error'));
}

if (is_callable('hypeapps_attach_uploaded_files')) {
	$uploads = hypeapps_attach_uploaded_files($post, 'uploads', [
		'origin' => 'wall',
		'container_guid' => $post->guid,
		'access_id' => $post->access_id,
	]);
}

foreach ($upload_guids as $upload_guid) {
	$upload = get_entity($upload_guid);
	if (!$upload instanceof ElggFile || !$upload->canEdit()) {
		continue;
	}
	$upload->origin = 'wall';
	$upload->container_guid = $post->guid;
	$upload->access_id = $post->access_id;
	if ($upload->save()) {
		$uploads[] = $upload;
		$attachment_guids[] = $upload->guid;
	}
}

if (is_callable('hypeapps_attach')) {
	foreach ($attachment_guids as $attachment_guid) {
		$attachment = get_entity($attachment_guid);
		if (!$attachment) {
			continue;
		}
		hypeapps_attach($post, $attachment);
	}
}

// Add 'tagged_in' relationships
// If the access level for the post is not set to private, also create a river item
// with the access level specified in their settings by the tagged user
foreach ($friend_guids as $friend_guid) {
	$friend = get_entity($friend_guid);
	if (!$friend) {
		continue;
	}

	$new_tag = add_entity_relationship($friend->guid, 'tagged_in', $post->guid);
	add_entity_relationship($post->guid, 'access_grant', $friend->guid);

	foreach ($uploads as $upload) {
		add_entity_relationship($upload->guid, 'access_grant', $friend->guid);
	}

	$river_access_id = elgg_get_plugin_user_setting('river_access_id', $friend->guid, 'hypeWall', ACCESS_FRIEND);
	if ($river_access_id && $new_tag) {
		$ia = elgg_set_ignore_access(true);
		$friend_wall_tag = elgg_get_entities([
			'types' => 'object',
			'subtypes' => 'wall_tag',
			'owner_guids' => $friend->guid,
			'container_guids' => $post->guid,
			'count' => true,
		]);
		if (!$friend_wall_tag) {
			$friend_wall_tag = new ElggObject();
			$friend_wall_tag->subtype = 'wall_tag';
			$friend_wall_tag->owner_guid = $friend->guid;
			$friend_wall_tag->container_guid = $post->guid;
			$friend_wall_tag->access_id = $river_access_id;
			$friend_wall_tag->relationship_id = $new_tag;
			$friend_wall_tag->save();

			elgg_create_river_item(array(
				'view' => 'river/relationship/tagged/create',
				'action_type' => 'tagged',
				'subject_guid' => $friend->guid,
				'object_guid' => $friend_wall_tag->guid,
			));
		}
		elgg_set_ignore_access($ia);
	}
}

$make_bookmark = function() use ($poster, $container, $address, $post) {
	if (!$address) {
		return false;
	}
	if (!get_input('make_bookmark') || !elgg_is_active_plugin('bookmarks')) {
		return false;
	}
	if (!is_callable('hypeapps_scrape')) {
		return false;
	}
	if (!$container->canWriteToContainer($poster->guid, 'object', 'bookmarks')) {
		return false;
	}

	$document = hypeapps_scrape($address);
	$title = strip_tags(elgg_extract('title', $document, ''));
	if (empty($title)) {
		return false;
	}

	$bookmark = new ElggObject;
	$bookmark->subtype = "bookmarks";
	$bookmark->owner_guid = $poster->guid;
	$bookmark->container_guid = $container->guid;
	$bookmark->address = $address;
	$bookmark->access_id = $post->access_id;
	$bookmark->origin = 'wall';
	$bookmark->title = htmlentities($title, ENT_QUOTES, 'UTF-8');
	$bookmark->description = strip_tags(elgg_extract('description', $document, ''));
	$bookmark->tags = elgg_extract('keywords', $document);
	$bookmark->save();
};

$make_bookmark();

if ($action == 'create') {
	// Create a river entry for this wall post
	elgg_create_river_item(array(
		'view' => 'river/object/hjwall/create',
		'action_type' => 'create',
		'subject_guid' => $post->owner_guid,
		'object_guid' => $post->guid,
		'target_guid' => $post->container_guid,
	));

	// Trigger a publish event, so that we can send out notifications
	elgg_trigger_event('publish', 'object', $post);
}

$params = array(
	'entity' => $post,
	'user' => $poster,
	'message' => $status,
	'url' => $post->getURL(),
	'origin' => 'wall',
);
elgg_trigger_plugin_hook('status', 'user', $params);

$data = '';
if (elgg_is_xhr()) {
	$data = elgg_list_river(array(
		'object_guids' => $post->guid,
		'pagination' => false,
		'limit' => 0,
	));
}

$msg = elgg_echo('wall:create:success');

if ($container instanceof ElggUser) {
	$forward_url = elgg_normalize_url("wall/owner/{$container->username}");
} else {
	$forward_url = elgg_normalize_url("wall/container/{$container->guid}");
}

return elgg_ok_response($data, $msg, $forward_url);
