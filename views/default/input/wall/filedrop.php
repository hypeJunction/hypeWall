<?php

/**
 * Creates drag&drop file uploader
 * In non-html5 browsers falls back to regular file input
 *
 * On upload success, new file entity guid is stored in hidden inputs that submitted with the form in uploads[] array
 * @uses $vars['allowedfiletypes'] Options. Can be used to define allowed mimetypes
 *
 */
elgg_load_js('jquery.filedrop.js');
elgg_load_js('wall.filedrop');
elgg_load_css('wall.filedrop');

$time = elgg_extract('batch_upload_time', $vars);

$allowedfiletypes = isset($vars['allowedfiletypes']) ? $vars['allowedfiletypes'] : array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
$types = htmlentities(json_encode($allowedfiletypes));

$time = md5(microtime());
$attr = elgg_format_attributes(array(
	'id' => 'wall-filedrop-' . $time,
	'class' => 'wall-filedrop-container',
	'data-filetypes' => $types,
	'data-container-guid' => elgg_extract('container_guid', $vars, null),
	'data-batch-time' => $time
		));

$fallback_link = elgg_view('output/url', array(
	'text' => elgg_echo('wall:filedrop:fallback'),
	'href' => '#wall-filedrop-' . $time,
	'class' => 'wall-filedrop-fallback-trigger'
		));

$instructions = elgg_echo('wall:filedrop:instructions', array($fallback_link));

$fallback = elgg_view('input/file', array(
	'id' => 'wall-fd-fallback-' . $time,
	'name' => 'files[]',
	'multiple' => true,
	'class' => 'wall-fd-fallback hidden',
		));

echo <<<__HTML
<div class="wall-filedrop-wrap">
	<div $attr>
		<div class="wall-filedrop">
			<span class="wall-filedrop-message">$instructions</span>
			$fallback
		</div>
		<div class="wall-filedrop-queue">
		</div>
		<div class="wall-template hidden">
			<div class="wall-media-summary">
				<div class="wall-unknown-placeholder">
					<img />
					<span class="elgg-state-uploaded"></span>
					<span class="elgg-state-failed"></span>
				</div>
				<div class="wall-filedrop-progressholder">
					<div class="wall-filedrop-progress"></div>
				</div>
			</div>
		</div>
	</div>
</div>
__HTML;
