<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>

	elgg.provide('framework');
	elgg.provide('framework.wall');
	elgg.provide('framework.wall.filedrop');

	framework.wall.filedrop.init = function() {
		$('[id^="wall-filedrop"]').live('build', framework.wall.filedrop.build);
		$('[id^="wall-filedrop"]').trigger('build');
	}

	framework.wall.filedrop.build = function() {

		var $filedrop = $(this);

		var container_guid = $filedrop.data('containerGuid');

		framework.wall.filedrop.template = $('.wall-template', $filedrop).html();

		$('.wall-filedrop-fallback-trigger', $filedrop).live('click', function(e) {
			e.preventDefault();
			$('.wall-fd-fallback', $filedrop).trigger('click');
		})

		$filedrop.filedrop({
					fallback_id: 'wall-filedrop-fallback',
					url: elgg.security.addToken(elgg.normalize_url('action/wall/upload?container_guid=' + container_guid)),
					paramname: 'filedrop_files',
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					},
					//allowedfiletypes : $filedrop.data('allowedfiletypes'),
					queuefiles: 100,
					maxfiles: 100,
					maxfilesize: <?php echo (int) ini_get('upload_max_filesize') ?>,
					uploadFinished: function(i, file, response) {

						if (response.status >= 0) {

							$('#wall-filedrop-fallback').val(''); // in case upload was triggered by fallback

							$.data(file).find('.elgg-state-uploaded').show();
							$.data(file).find('.wall-filedrop-progressholder').replaceWith($(response.output.form)).find('input').focus();

							$.data(file).closest('form').find('[type="submit"]').show();

							$.data(file).after($('<input>').attr({type: 'hidden', name: 'upload_guids[]'}).val(response.output.file_guid));

							elgg.trigger_hook('ajax:success', 'framework', {response: response, element: $filedrop});
							elgg.ui.initDatePicker();

						} else {

							$.data(file).find('.elgg-state-failed').show();
							$.data(file).find('.wall-filedrop-progressholder').replaceWith($('<p>').addClass('wall-item-in-bulk').text(response.system_messages.error.join('')));
						}
					},
					error: function(err, file) {
						switch (err) {
							case 'BrowserNotSupported':
								$('#wall-filedrop-fallback').show().unbind('change');
								elgg.register_error(elgg.echo('hj:wall:filedrop:browsernotsupported'));
								break;

							case 'TooManyFiles':
								elgg.register_error(elgg.echo('hj:wall:filedrop:toomanyfiles'));
								break;

							case 'FileTooLarge':
								elgg.register_error(elgg.echo('hj:wall:filedrop:filetoolarge'));
								break;

							case 'FileTypeNotAllowed':
								elgg.register_error(elgg.echo('hj:wall:filedrop:filetypenotallowed'));
								break;

							default:
								break;
						}
					},
					beforeEach: function(file) {

					},
					uploadStarted: function(i, file, len) {
						if (file.type.match(/^image\//)) {
							framework.wall.filedrop.createImage(file, $filedrop);
						} else if (file.type.match(/^video\//)) {
							framework.wall.filedrop.createVideo(file, $filedrop);
						} else if (file.type.match(/^audio\//)) {
							framework.wall.filedrop.createAudio(file, $filedrop);
						} else {
							framework.wall.filedrop.createPlaceholder(file, $filedrop);
						}
					},
					progressUpdated: function(i, file, progress) {
						$.data(file).find('.wall-filedrop-progress').width(progress);
					}

				});

	}

	framework.wall.filedrop.createImage = function(file, $container) {

		var $preview = $(framework.wall.filedrop.template),
				$image = $('img', $preview);

		var reader = new FileReader();

		$image.width(150);

		reader.onload = function(e) {

			// e.target.result holds the DataURL which
			// can be used as a source of the image:

			$image.attr('src', e.target.result);
		};

		// Reading the file as a DataURL. When finished,
		// this will trigger the onload function above:
		reader.readAsDataURL(file);

		$preview.appendTo($('.wall-filedrop-queue', $container));

		// Associating a preview container
		// with the file, using jQuery's $.data():

		$.data(file, $preview);
	}


	framework.wall.filedrop.createVideo = function(file, $container) {

		var $preview = $(framework.wall.filedrop.template),
				$image = $('img', $preview);

		var reader = new FileReader();

		$image.replaceWith($('<video>').attr({width: 300, height: 200, controls: true}).html($('<source>')));

		reader.onload = function(e) {

			// e.target.result holds the DataURL which
			// can be used as a source of the image:

			$image.find('source').attr('src', e.target.result).attr('type', file.type);
		};

		// Reading the file as a DataURL. When finished,
		// this will trigger the onload function above:
		reader.readAsDataURL(file);

		$preview.appendTo($('.wall-filedrop-queue', $container));

		// Associating a preview container
		// with the file, using jQuery's $.data():

		$.data(file, $preview);
	}



	framework.wall.filedrop.createAudio = function(file, $container) {

		var $preview = $(framework.wall.filedrop.template),
				$image = $('img', $preview);

		var reader = new FileReader();

		$image.replaceWith($('<audio>').attr({controls: true}).html($('<source>')));

		reader.onload = function(e) {

			// e.target.result holds the DataURL which
			// can be used as a source of the image:

			$image.find('source').attr('src', e.target.result).attr('type', file.type);
		};

		// Reading the file as a DataURL. When finished,
		// this will trigger the onload function above:
		reader.readAsDataURL(file);

		$preview.appendTo($('.wall-filedrop-queue', $container));

		// Associating a preview container
		// with the file, using jQuery's $.data():

		$.data(file, $preview);
	}


	framework.wall.filedrop.createPlaceholder = function(file, $filedrop) {

		var $preview = $(framework.wall.filedrop.template),
				$image = $('img', $preview);

		var reader = new FileReader();

		$image.width(300);

		$image.attr('src', elgg.get_site_url() + '_graphics/spacer.gif');

		reader.readAsDataURL(file);

		$preview.appendTo($('.wall-filedrop-queue', $filedrop));

		$.data(file, $preview);
	}

	elgg.register_hook_handler('init', 'system', framework.wall.filedrop.init);

<?php if (FALSE) : ?></script><?php
endif;
?>
