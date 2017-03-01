define(function (require) {

	var elgg = require('elgg');
	var $ = require('jquery');
	require('jquery.form');

	var wall = {
		/**
		 * Bind events to DOM elements
		 * @returns void
		 */
		init: function () {

			if (typeof navigator === 'undefined') {
				$('.wall-find-me').hide();
			}

			$('.wall-form').removeAttr('onsubmit')

			$(document).on('click.wall', '.wall-find-me', wall.findMe);
			$(document).on('click.wall', '.wall-tab', wall.switchTab);
			$(document).on('keyup.wall keydown.wall', 'textarea[data-limit]', wall.updateCounter);
			$(document).on('keyup.wall', '.wall-input-status', wall.parseUrl);
			$(document).on('blur.wall focusout.wall preview.wall clear.wall', '.wall-url', wall.loadUrlPreview);
			$(document).on('submit.wall', '.wall-form:not(.wall-form-edit)', wall.formSubmit);

			$(document).on('click', '.elgg-menu-wall-tools-default > li > a', function (e) {
				e.preventDefault();
				var $form = $(this).closest('form');
				var href = $(this).data('section');
				$(href, $form).removeClass('hidden');
				$(this).parent().addClass('hidden');
			});

			wall.init = elgg.nullFunction();
		},
		findMe: function (e) {
			e.preventDefault();
			var $form = $(this).closest('form');
			navigator.geolocation.getCurrentPosition(function (position) {
				$.ajax({
					crossDomain: true,
					dataType: "json",
					url: '//nominatim.openstreetmap.org/reverse',
					data: {
						format: 'json',
						lat: position.coords.latitude,
						lon: position.coords.longitude,
						addressdetails: 0,
						zoom: 12
					},
					success: function (data) {
						$form.find('.wall-input-location').val(data.display_name);
					}
				});
			});
		},
		/**
		 * Switch wall form when tab link is clicked
		 * @param object e
		 * @returns void
		 */
		switchTab: function (e) {
			e.preventDefault();
			var $tab = $(this);
			$tab.closest('li').toggleClass('elgg-state-selected').siblings().removeClass('elgg-state-selected');
			var $form = $($tab.attr('href'));
			$('.wall-form').addClass('hidden');
			$form.removeClass('hidden');
		},
		/**
		 * Parse URLs from the user input and add them to the URL input field
		 * @param object e
		 * @returns void
		 */
		parseUrl: function (e) {

			var $form = $(this).closest('form');
			var $url = $form.find('.wall-url');
			if ($url.val()) {
				return;
			}

			var text = $(this).val();
			var match = text.match(/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig);
			if (!match) {
				return;
			}

			if (match instanceof Array) {
				var url = match[0];
			} else {
				var url = match;
			}

			if (url.length) {
				$url.val(url).trigger('preview');
			}
		},
		/**
		 * Loads the preview of the
		 * @param object e
		 * @returns void
		 */
		loadUrlPreview: function (e) {
			var $elem = $(this);
			var $form = $elem.closest('form');
			var $preview = $form.find('.wall-url-preview');
			var url = $elem.val();
			if (!url) {
				wall.loadedUrlPreview = null;
				$preview.html('');
			} else if (url !== wall.loadedUrlPreview) {
				elgg.ajax('ajax/view/output/wall/url', {
					dataType: 'html',
					data: {
						value: url
					},
					beforeSend: function () {
						$preview.addClass('elgg-state-loading');
					},
					success: function (data) {
						$preview.html(data);
						wall.loadedUrlPreview = url;
						wall.loadedPreviewHtml = data;
					}
				});
			} else if (!$preview.html()) {
				$preview.html(wall.loadedPreviewHtml);
			}
		},
		/**
		 * Submit a form via AJAX and populate the river with new entries
		 * @param object event
		 * @returns void
		 */
		formSubmit: function (event) {

			event.preventDefault();

			var $form = $(this);

			$form.ajaxSubmit({
				//iframe: $form.is('[enctype^="multipart"]'),
				dataType: 'json',
				data: {
					container_guid: elgg.get_page_owner_guid(),
					river: $form.closest('.wall-container').is('.wall-river'),
					widget: $form.closest('.elgg-widgets').length
				},
				beforeSend: function () {
					$form.find('[type="submit"]').addClass('elgg-state-disabled').text(elgg.echo('wall:process:posting')).prop('disabled', true);
					$('body').addClass('elgg-state-loading');
				},
				success: function (data) {
					if (data.status >= 0) {
						$form.resetForm();
						if ($('.elgg-input-tokeninput', $form).length) {
							$('.elgg-input-tokeninput', $form).bind('clear', function (e) {
								$(this).tokenInput("clear");
							}).trigger('clear');
						}
						$('.elgg-dropzone-preview', $form).remove();
						$('.token-input-dropdown').hide();
						$form.find('.wall-url').val('').trigger('clear');
						$form.find('textarea:first').trigger('click');
						$form.find('[data-section]').each(function() {
							$(this).parent().removeClass('hidden');
							var href = $(this).data('section');
							$(href, $form).addClass('hidden');
						});
						if ($('.elgg-list-river,.wall-post-list').length > 1) {
							$('[data-list-id="wall-' + elgg.get_page_owner_guid() + '"] > .elgg-list').children('.elgg-list').trigger('addFetchedItems', [data.output, null, true]);
						} else {
							$('.elgg-list-river,.wall-post-list').trigger('addFetchedItems', [data.output, null, true]);
						}
						$('.elgg-list-river,.wall-post-list').trigger('refresh', [null, false]);
					}
					if (data.system_messages) {
						elgg.register_error(data.system_messages.error);
						elgg.system_message(data.system_messages.success);
					}
				},
				error: function () {
					elgg.register_error(elgg.echo('wall:error:ajax'));
				},
				complete: function () {
					$('body').removeClass('elgg-state-loading');
					$form.find('[type="submit"]').removeClass('elgg-state-disabled').text(elgg.echo('wall:post')).prop('disabled', false);
				}
			});
		},
		/**
		 * Update char limit counter
		 * @param {object} e
		 * @returns {void}
		 */
		updateCounter: function (e) {

			var $textarea = $(this);
			var limit = $textarea.data('limit');
			var remaining = limit - $textarea.val().length;
			var $form = $textarea.closest('form')
			var $counter = $form.find('[data-counter]').eq(0);
			$counter.find('[data-counter-indicator]').text(remaining);
			if (remaining < 0) {
				$counter.addClass('wall-status-counter-overflow');
				$form.find('[type="submit"]').prop('disabled', true).addClass('elgg-state-disabled');
			} else {
				$counter.removeClass('wall-status-counter-overflow');
				$form.find('[type="submit"]').prop('disabled', false).removeClass('elgg-state-disabled');
			}
		}
	};
	return wall;
});
