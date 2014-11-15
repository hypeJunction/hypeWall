<?php

namespace hypeJunction\Wall;

use ElggBatch;
use ElggObject;

/**
 * Callback function for token input search
 *
 * @param string $term    Search term
 * @param array  $options Options
 * @return array
 */
function search_locations($term, $options = array()) {

	$term = sanitize_string($term);

	$query = str_replace(array('_', '%'), array('\_', '\%'), $term);

	$options['metadata_names'] = array('location', 'temp_location');
	$options['group_by'] = "v.string";
	$options['wheres'] = array("v.string LIKE '%$query%'");

	return elgg_get_metadata($options);
}

/**
 * Get coordinates and location name of the current session
 * @return array
 */
function get_geopositioning() {
	if (isset($_SESSION['geopositioning'])) {
		return $_SESSION['geopositioning'];
	}
	return array(
		'location' => '',
		'latitude' => 0,
		'longitude' => 0
	);
}

/**
 * Set session geopositioning
 * Cache geocode along the way
 * 
 * @param string $location  Location
 * @param float  $latitude  Latitude
 * @param float  $longitude Longitude
 * @return void
 */
function set_geopositioning($location = '', $latitude = 0, $longitude = 0) {

	$location = sanitize_string($location);
	$lat = (float) $latitude;
	$long = (float) $longitude;

	if (!$lat || !$long) {
		$latlong = elgg_trigger_plugin_hook('geocode', 'location', array('location' => $location));
		if ($latlong) {
			$lat = elgg_extract('lat', $latlong);
			$long = elgg_extract('long', $latlong);
		}
	}

	$_SESSION['geopositioning'] = array(
		'location' => $location,
		'latitude' => $lat,
		'longitude' => $long
	);
}

/**
 * Get a wall post message suitable for notifications and status updates
 * 
 * @param ElggObject $object          Wall or wire post
 * @param bool       $include_address Include URL address in the message body
 * @return string
 */
function format_wall_message($object, $include_address = false) {

	$status = $object->description;
	$status = elgg_trigger_plugin_hook('link:qualifiers', 'wall', array('source' => $status), $status);

	$message = array(0 => $status);

	$tagged_friends = get_tagged_friends($object, 'links');
	if ($tagged_friends) {
		$message[2] = '<span class="wall-tagged-friends">' . elgg_echo('wall:with', array(implode(', ', $tagged_friends))) . '</span>';
	}

	$location = $object->getLocation();
	if ($location) {
		$location = elgg_view('output/wall/location', array('value' => $location));
		$message[3] = '<span class="wall-tagged-location">' . elgg_echo('wall:at', array($location)) . '</span>';
	}

	if (!$status || $include_address) {
		$address = $object->address;
		if ($address && (strpos($status, $address) === false)) {
			$message[1] = elgg_view('output/url', array(
				'href' => $address,
				'class' => 'wall-attached-url',
			));
		}
	}

	ksort($message);

	$output = implode(' ', $message);
	return elgg_trigger_plugin_hook('message:format', 'wall', array('entity' => $object), $output);
}

/**
 * Prepare wall post attachments
 *
 * @param ElggObject $object Wall post
 * @return string|false
 */
function format_wall_attachments($object) {

	$attachments = array();

	if ($object->address) {
		$attachments[] = elgg_view('output/wall/url', array(
			'value' => $object->address,
		));
	}

	$attachments[] = $object->html;

	$attachments[] = elgg_view('output/wall/attachments', array(
		'entitiy' => $object,
	));

	return (count($attachments)) ? implode('', $attachments) : false;
}

/**
 * Prepare wall river summary
 *
 * @param ElggObject $object Wall or wire post
 * @return string
 */
function format_wall_summary($object) {

	$subject = $object->getOwnerEntity();
	$wall_owner = $object->getContainerEntity();

	if ($wall_owner->guid == $subject->guid || $wall_owner->guid == elgg_get_page_owner_guid()) {
		$owned = true;
	}

	if (elgg_instanceof($wall_owner, 'group')) {
		$group_wall = true;
	}

	$summary[] = elgg_view('output/url', array(
		'text' => $subject->name,
		'href' => $subject->getURL(),
		'class' => 'elgg-river-subject',
	));

	if ($object->address) {
		$summary[] = elgg_echo('wall:new:address');
	} else {
		$files = elgg_get_entities_from_relationship(array(
			'relationship' => 'attached',
			'relationship_guid' => $object->guid,
			'inverse_relationship' => true,
			'count' => true,
		));
		if ($files) {
			$images = elgg_get_entities_from_relationship(array(
				'types' => 'object',
				'subtypes' => 'file',
				'metadata_name_value_pairs' => array(
					'name' => 'simpletype', 'value' => 'image',
				),
				'relationship' => 'attached',
				'relationship_guid' => $object->guid,
				'inverse_relationship' => true,
				'count' => true,
			));
			if ($files == $images) {
				$summary[] = elgg_echo('wall:new:images', array($images));
			} else if (!$images) {
				$summary[] = elgg_echo('wall:new:items', array($files));
			} else {
				$summary[] = elgg_echo('wall:new:attachments', array($images, $files - $images));
			}
		} else if (!$owned && !$group_wall) {
			$summary[] = elgg_echo('wall:new:status');
		}
	}

	if (!$owned && !$group_wall) {
		$wall_owner_link = elgg_view('output/url', array(
			'text' => $wall_owner->name,
			'href' => $wall_owner->getURL(),
			'class' => 'elgg-river-object',
		));
		$summary[] = elgg_echo('wall:owner:suffix', array($wall_owner_link));
	}

	return implode(' ', $summary);
}

/**
 * Get tagged friends
 *
 * @param ElggObject $object Wall or wire post
 * @param string     $format links|icons or null for an array of entities
 * @param size       $size   Icon size
 * @return string
 */
function get_tagged_friends($object, $format = null, $size = 'small') {

	$tagged_friends = array();

	$tags = new ElggBatch('elgg_get_entities_from_relationship', array(
		'types' => 'user',
		'relationship' => 'tagged_in',
		'relationship_guid' => $object->guid,
		'inverse_relationship' => true,
		'limit' => false
	));

	foreach ($tags as $tag) {
		if ($format == 'links') {
			$tagged_friends[] = elgg_view('output/url', array(
				'text' => (isset($tag->name)) ? $tag->name : $tag->title,
				'href' => $tag->getURL(),
				'is_trusted' => true
			));
		} else if ($format == 'icons') {
			$tagged_friends[] = elgg_view_entity_icon($tag, $size, array(
				'class' => 'wall-post-tag-icon',
				'use_hover' => false
			));
		} else {
			$tagged_friends[] = $tag;
		}
	}

	return $tagged_friends;
}

/**
 * Get attachments
 *
 * @param ElggObject $object Wall or wire post
 * @param string     $format links|icons or null for an array of entities
 * @param size       $size   Icon size
 * @return string
 */
function get_attachments($object, $format = null, $size = 'small') {

	$attachment_tags = array();

	$attachments = new ElggBatch('elgg_get_entities_from_relationship', array(
		'relationship' => 'attached',
		'relationship_guid' => $object->guid,
		'inverse_relationship' => true,
		'limit' => false
	));

	foreach ($attachments as $attachment) {
		if ($format == 'links') {
			$attachment_tags[] = elgg_view('output/url', array(
				'text' => (isset($attachment->name)) ? $attachment->name : $attachment->title,
				'href' => $attachment->getURL(),
				'is_trusted' => true
			));
		} else if ($format == 'icons') {
			$attachment_tags[] = elgg_view_entity_icon($attachment, $size, array(
				'class' => 'wall-post-tag-icon',
				'use_hover' => false
			));
		} else {
			$attachment_tags[] = $attachment;
		}
	}

	return $attachment_tags;
}

/**
 * Extract hashtags from a text
 *
 * @param string $text Source text
 * @return array
 */
function get_hashtags($text) {
	$tags = array();
	preg_match_all('/(^|[^\w])#(\w*[^\s\d!-\/:-@]+\w*)/', $text, $tags);
	return $tags[2];
}

/**
 * Get an array of wall subtypes
 * @return array
 */
function get_wall_subtypes() {
	return array_unique(array(WALL_SUBTYPE, 'hjwall'));
}
