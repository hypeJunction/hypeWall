<?php

namespace hypeJunction\Wall;

use ElggBatch;
use ElggEntity;
use ElggFile;
use ElggObject;

/**
 * Callback function for token input search
 *
 * @param string $term
 * @param array $options
 * @return array
 */
function search_locations($term, $options = array()) {

	$term = sanitize_string($term);

	$q = str_replace(array('_', '%'), array('\_', '\%'), $term);

	$options['metadata_names'] = array('location', 'temp_location');
	$options['group_by'] = "v.string";
	$options['wheres'] = array("v.string LIKE '%$q%'");

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
 * @param string $location
 * @param float $latitude
 * @param float $longitude
 * @return void
 */
function set_geopositioning($location = '', $latitude = 0, $longitude = 0) {

	$location = sanitize_string($location);
	$lat = (float) $latitude;
	$long = (float) $longitude;

	$latlong = elgg_geocode_location($location);
	if ($latlong) {
		$latitude = elgg_extract('lat', $latlong);
		$longitude = elgg_extract('long', $latlong);
	} else if ($location && $latitude && $longitude) {
		$dbprefix = elgg_get_config('dbprefix');
		$query = "INSERT INTO {$dbprefix}geocode_cache
				(location, lat, `long`) VALUES ('$location', '{$lat}', '{$long}')
				ON DUPLICATE KEY UPDATE lat='{$lat}', `long`='{$long}'";

		insert_data($query);
	}

	$_SESSION['geopositioning'] = array(
		'location' => $location,
		'latitude' => (float) $latitude,
		'longitude' => (float) $longitude
	);
}

/**
 * Get a wall post message suitable for notifications and status updates
 * @param ElggObject $object
 * @param bool $include_address Include URL address in the message body
 * @return string
 */
function format_wall_message($object, $include_address = false) {

	$status = $object->description;

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

	$attachments = get_attachments($object, 'links');
	if ($attachments) {
		$message[4] = '<span class="wall-tagged-attachments">' . elgg_echo('wall:attached', array(count($attachments))) . '</span>';
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
 * Get tagged friends
 *
 * @param ElggObject $object
 * @param string $format	links|icons or null for an array of entities
 * @param size $size  Icon size
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
 * Get a string of wall tags
 *
 * @param ElggObject $object
 * @param string $format	links|icons or null for an array of entities
 * @param size $size  Icon size
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
 * Process uploaded files
 *
 * @param string $name			Name of the HTML file input
 * @param string $subtype		Object subtype to be assigned to newly created objects
 * @param type $guid			GUID of an existing object
 * @param type $container_guid	GUID of the container entity
 * @return array				An associative array of original file names and guids (or false) of created object
 */
function process_file_upload($name, $subtype = 'file', $guid = null, $container_guid = null) {

	// Normalize the $_FILES array
	if (is_array($_FILES[$name]['name'])) {
		$files = prepare_files_global($_FILES);
		$files = $files[$name];
	} else {
		$files = $_FILES[$name];
		$files = array($files);
	}

	foreach ($files as $file) {
		if (!is_array($file) || $file['error']) {
			continue;
		}

		$filehandler = new ElggFile($guid);
		$prefix = 'hjfile/';

		if ($guid) {
			$filename = $filehandler->getFilenameOnFilestore();
			if (file_exists($filename)) {
				unlink($filename);
			}
			$filestorename = $filehandler->getFilename();
			$filestorename = elgg_substr($filestorename, elgg_strlen($prefix));
		} else {
			$filehandler->subtype = $subtype;
			$filehandler->container_guid = $container_guid;
			$filestorename = elgg_strtolower(time() . $file['name']);
		}

		$filehandler->setFilename($prefix . $filestorename);
		$filehandler->title = $file['name'];

		$mime_type = ElggFile::detectMimeType($file['tmp_name'], $file['type']);

		// hack for Microsoft zipped formats
		$info = pathinfo($file['name']);
		$office_formats = array('docx', 'xlsx', 'pptx');
		if ($mime_type == "application/zip" && in_array($info['extension'], $office_formats)) {
			switch ($info['extension']) {
				case 'docx':
					$mime_type = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
					break;
				case 'xlsx':
					$mime_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
					break;
				case 'pptx':
					$mime_type = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
					break;
			}
		}

		// check for bad ppt detection
		if ($mime_type == "application/vnd.ms-office" && $info['extension'] == "ppt") {
			$mime_type = "application/vnd.ms-powerpoint";
		}

		$filehandler->setMimeType($mime_type);

		$filehandler->originalfilename = $file['name'];
		$filehandler->simpletype = get_simple_type($mime_type);
		$filehandler->filesize = $file['size'];

		$filehandler->open("write");
		$filehandler->close();

		move_uploaded_file($file['tmp_name'], $filehandler->getFilenameOnFilestore());

		if ($filehandler->save()) {

			// Generate icons for images
			if ($filehandler->simpletype == "image") {
				generate_entity_icons($filehandler);
			}

			$return[$file['name']] = $filehandler->getGUID();
		} else {
			$return[$file['name']] = false;
		}
	}

	return $return;
}

/**
 * Normalize $_FILES global
 *
 * @param array $_files
 * @param bool $top
 * @return array
 */
function prepare_files_global(array $_files, $top = TRUE) {

	$files = array();
	foreach ($_files as $name => $file) {
		if ($top) {
			$sub_name = $file['name'];
		} else {
			$sub_name = $name;
		}
		if (is_array($sub_name)) {
			foreach (array_keys($sub_name) as $key) {
				$files[$name][$key] = array(
					'name' => $file['name'][$key],
					'type' => $file['type'][$key],
					'tmp_name' => $file['tmp_name'][$key],
					'error' => $file['error'][$key],
					'size' => $file['size'][$key],
				);
				$files[$name] = prepare_files_global($files[$name], FALSE);
			}
		} else {
			$files[$name] = $file;
		}
	}
	return $files;
}

/**
 * Generate icons for an entity
 *
 * @param ElggEntity $entity
 * @param ElggFile $filehandler		Valid $filehandler on Elgg filestore to grab the file from | can be null if $entity is instance of ElggFile
 * @param array $coords				Coordinates for cropping
 * @return boolean
 */
function generate_entity_icons($entity, $filehandler = null, $coords = null) {

	if (!$filehandler && $entity instanceof ElggFile) {
		$filehandler = $entity;
	}

	if (!$filehandler) {
		return false;
	}

	$prefix = "icons/" . $entity->getGUID();
	$filestorename = $filehandler->getFilename();

	$filehandler->icontime = time();

	$thumbnail = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(), 60, 60, true);
	if ($thumbnail) {
		$thumb = new ElggFile();
		$thumb->setFilename($prefix . "thumb" . $filestorename);
		$thumb->open("write");
		$thumb->write($thumbnail);
		$thumb->close();

		$filehandler->thumbnail = $prefix . "thumb" . $filestorename;
		unset($thumbnail);
	}

	$thumbsmall = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(), 153, 153, true);
	if ($thumbsmall) {
		$thumb->setFilename($prefix . "smallthumb" . $filestorename);
		$thumb->open("write");
		$thumb->write($thumbsmall);
		$thumb->close();
		$filehandler->smallthumb = $prefix . "smallthumb" . $filestorename;
		unset($thumbsmall);
	}

	$thumblarge = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(), 600, 600, false);
	if ($thumblarge) {
		$thumb->setFilename($prefix . "largethumb" . $filestorename);
		$thumb->open("write");
		$thumb->write($thumblarge);
		$thumb->close();
		$filehandler->largethumb = $prefix . "largethumb" . $filestorename;
		unset($thumblarge);
	}

	return $filehandler->icontime;
}

/**
 * Copy of file_get_simple_type()
 * Redefined in case file plugin is disabled
 *
 * @param string $mimetype
 * @return string
 */
function get_simple_type($mimetype) {

	switch ($mimetype) {
		case "application/msword":
		case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
			return "document";
			break;
		case "application/pdf":
			return "document";
			break;
		case "application/ogg":
			return "audio";
			break;
	}

	if (substr_count($mimetype, 'text/')) {
		return "document";
	}

	if (substr_count($mimetype, 'audio/')) {
		return "audio";
	}

	if (substr_count($mimetype, 'image/')) {
		return "image";
	}

	if (substr_count($mimetype, 'video/')) {
		return "video";
	}

	if (substr_count($mimetype, 'opendocument')) {
		return "document";
	}

	return "general";
}
