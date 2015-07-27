<?php

namespace hypeJunction\Wall;

/**
 * Callback function for token input search
 *
 * @param string $term    Search term
 * @param array  $options Options
 * @return array
 */
function search_locations($term, $options = array()) {
	return hypeWall()->geo->search($term, $options);
}


/**
 * Get coordinates and location name of the current session
 * @return array
 */
function get_geopositioning() {
	return hypeWall()->geo->get();
}

/**
 * Set session geopositioning
 * Cache geocode along the way
 *
 * @param string $location
 * @param float $latitude
 * @param float $longitude
 * @return void
 */
function set_geopositioning($location = '', $latitude = 0, $longitude = 0) {
	hypeWall()->geo->set($location, $latitude, $longitude);
}

/**
 * Get a wall post message suitable for notifications and status updates
 * 
 * @param \ElggObject $object          Wall or wire post
 * @param bool        $include_address Include attached URL address in the message body
 * @return string
 */
function format_wall_message($object, $include_address = false) {
	if ($object instanceof Post) {
		return $object->formatMessage($include_address);
	}
	return $object->description;
}

/**
 * Prepare wall post attachments
 *
 * @param \ElggObject $object Wall post
 * @return string|false
 */
function format_wall_attachments($object) {

	if ($object instanceof Post) {
		return $object->formatAttachments();
	}
	return '';
}

/**
 * Prepare wall river summary
 *
 * @param \ElggObject $object Wall or wire post
 * @return string
 */
function format_wall_summary($object) {

	if ($object instanceof Post) {
		return $object->formatSummary();
	}

	return '';
}

/**
 * Get tagged friends
 *
 * @param \ElggObject $object Wall or wire post
 * @param string      $format links|icons or null for an array of entities
 * @param size        $size   Icon size
 * @return string
 */
function get_tagged_friends($object, $format = null, $size = 'small') {

	if ($object instanceof Post) {
		return $object->getTaggedFriends($format, $size);
	}

	return '';
}

/**
 * Get attachments
 *
 * @param \ElggObject $object Wall or wire post
 * @param string      $format links|icons or null for an array of entities
 * @param size        $size   Icon size
 * @return string
 */
function get_attachments($object, $format = null, $size = 'small') {

	if ($object instanceof Post) {
		return $object->getAttachments($format, $size);
	}

	return '';
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
	return array_unique(array(hypeWall()->config->getPostSubtype(), 'hjwall'));
}
