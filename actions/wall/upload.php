<?php

$file = $_FILES['upload'];

if (!empty($file['name'])) {

	$filefolders = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'hjfilefolder',
		'owner_guid' => elgg_get_logged_in_user_guid(),
		'metadata_name' => 'handler',
		'metadata_value' => 'hjwall'
			));

	if (!$filefolders) {
		$filefolder = new ElggObject();
		$filefolder->title = elgg_echo('hj:wall:filefolder');
		$filefolder->subtype = 'hjfilefolder';
		$filefolder->handler = 'hjwall';
		$filefolder->datatype = 'default';
		$filefolder->data_pattern = hj_framework_get_data_pattern('object', 'hjfilefolder');
		$filefolder->owner_guid = elgg_get_logged_in_user_guid();
		$filefolder->container_guid = elgg_get_logged_in_user_guid();
		$filefolder->access_id = ACCESS_DEFAULT;
		$filefolder->save();

		hj_framework_set_entity_priority($filefolder);
	} else {
		$filefolder = $filefolders[0];
	}

	$filehandler = new hjFile();
	$filehandler->owner_guid = elgg_get_logged_in_user_guid();
	$filehandler->container_guid = $filefolder->getGUID();
	$filehandler->access_id = $filefolder->access_id;
	$filehandler->data_pattern = hj_framework_get_data_pattern('object', 'hjfile');
	$filehandler->title = elgg_echo('hj:wall:upload');
	$filehandler->description = '';

	$prefix = "hjfile/";

	$filestorename = elgg_strtolower($file['name']);

	$filehandler->setFilename($prefix . $filestorename);
	$filehandler->setMimeType($file['type']);
	$filehandler->originalfilename = $file['name'];
	$filehandler->simpletype = file_get_simple_type($file['type']);
	$filehandler->filesize = round($file['size'] / (1024 * 1024), 2) . "Mb";

	$filehandler->open("write");
	$filehandler->close();
	move_uploaded_file($file['tmp_name'], $filehandler->getFilenameOnFilestore());

	$file_guid = $filehandler->save();

	hj_framework_set_entity_priority($filehandler);

	if ($file_guid && $filehandler->simpletype == "image") {

		$thumb_sizes = array(
			'tiny' => 16,
			'small' => 25,
			'medium' => 40,
			'large' => 100,
			'preview' => 250,
			'master' => 500,
			'full' => 1024,
		);

		$thumb_sizes = elgg_trigger_plugin_hook('hj:framework:form:iconsizes', 'file', array('entity' => $formSubmission, 'field' => $field), $thumb_sizes);
		foreach ($thumb_sizes as $thumb_type => $thumb_size) {
			$square = false;
			if (in_array($thumb_type, array('tiny', 'small', 'medium', 'large'))) {
				$square = true;
			}
			$thumbnail = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(), $thumb_size, $thumb_size, $square, 0, 0, 0, 0, true);
			if ($thumbnail) {
				$thumb = new ElggFile();
				$thumb->setMimeType($file['type']);

				$thumb->setFilename("{$prefix}{$filehandler->getGUID()}{$thumb_type}.jpg");
				$thumb->open("write");
				$thumb->write($thumbnail);
				$thumb->close();

				$thumb_meta = "{$thumb_type}thumb";
				$filehandler->$thumb_meta = $thumb->getFilename();
				unset($thumbnail);
			}
		}
	}

	$html = elgg_view_entity($filehandler, array(
		'icon_size' => 'master',
		'full_view' => false
	));
	$html .= elgg_view('input/hidden', array(
		'name' => 'attachment',
		'value' => $filehandler->getGUID()
			));
}

header('Content-Type: application/json');
$output['output']['data'] = $html;
print(json_encode($output));
exit;