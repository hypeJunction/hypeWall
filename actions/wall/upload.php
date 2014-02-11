<?php

namespace hypeJunction\Wall;

$wall_collection_guid = get_input('container_guid');
$wall_collection = get_entity($wall_collection_guid);
if (!$wall_collection) {
	$wall_collection = elgg_get_logged_in_user_entity();
}

$guids = process_file_upload('filedrop_files', 'file', null, $wall_collection->guid);

$guid = reset($guids);

$file = get_entity($guid);

if (!$guid || $file->simpletype !== 'image') {
	if (elgg_instanceof($file)) {
		$file->delete();
	}
	register_error(elgg_echo('wall:upload:error'));
	forward(REFERER);
}

if (elgg_is_xhr()) {
	print json_encode(array(
		'file_guid' => $guid,
	));
}

forward(REFERER);
