<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>

	elgg.provide('hj.wall.base');

	hj.wall.base.init = function() {
		$('#hj-wall').tabs();
		
		var $status = $('#hj-wall').find('textarea[name="status"]');

		$status
		.keyup(function(){
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
					
			if (url && url.length > 0 && (!window.url_parsed || window.url_parsed[0] !== url)) {
				hj.wall.base.getUrlThumb(url);
			}


		});

		var source_entities = hj.wall.base.sourceentities;

		$('.hj-wall-tag-remove')
		.die()
		.live('click', function(event) {
			event.preventDefault();
			$(this).parents('li:first').remove();
		});


		$('.hj-wall-tags-autocomplete')
		.bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB &&
				$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				// delegate back to autocomplete, but extract the last term
				response( $.ui.autocomplete.filter(
				source_entities, request.term ) );
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				var tag = '<li class="hj-wall-tag clearfix">\n\
<span class="hj-left">' + ui.item.value + '</span>\n\
<a class="hj-wall-tag-remove hj-right"></a>\n\
<input type="hidden" name="wall_tag_guids[]" value="' + ui.item.guid + '" />\n\
</li>';
				var status_tag = '<span class="hj-wall-tag" rel="' + ui.item.guid + '"><a href="javascript:void(0)">' + ui.item.value + '</a></span>';
				$('ul#hj-wall-tags').append(tag);
				this.value = '';
				return false;
			}
		});

		$('.hj-wall-form-ajax')
		.attr('onsubmit','')
		.unbind('submit')
		.bind('submit', hj.wall.base.formSubmit);

		$('#hj-wall-photo-upload')
		.unbind('change')
		.bind('change', hj.wall.base.uploadFile);

		$('#hj-wall-file-upload')
		.unbind('change')
		.bind('change', hj.wall.base.uploadFile);
	}

	hj.wall.base.getUrlThumb = function(url) {
		if (!window.url_parsed) {
			window.url_parsed = new Array();
		}

		if (window.url_parsed[0] == url) {
			return;
		}
		window.url_parsed[0] = url;

		if (hj.wall.base.IsValidImageUrl(url, hj.wall.base.getImgMarkup)) {
			return;
		}

		if(url.substr(-1) == '/') {
			url = url.substr(0, url.length - 1);
		}
		urlDec = encodeURIComponent(url);

		var $img = $('<img>').attr('src', 'http://api.thumbalizr.com/?url=' + urlDec + '&width=500').attr('alt', '');
		var $link = $('<a>').attr('href', url).addClass('oembed').attr('target', '_blank').append($img);
		var $div = $('<div>').css('padding', '10px').css('border-bottom', '1px dashed #e8e8e8').append($link);

		var $hidden = $('<input>').attr('type', 'hidden').attr('name','attachment').val($div.html());
		$('#hj-wall').find('.hj-wall-form-attachment').empty().append($div).append($hidden);

		$("#hj-wall .oembed")
		.oembed(null,{
			embedMethod:'fill',
			maxWidth: 500
		})
		.addClass("oembed_init");

	}

	hj.wall.base.getImgMarkup = function (url, valid) {
		if (valid === true) {
			var $img = $('<img>').attr('src', url).addClass('elgg-photo').attr('alt', '').attr('width', '500');
			var $link = $('<a>').attr('href', url).attr('target', '_blank').append($img);
			var $div = $('<div>').css('padding', '10px').css('border-bottom', '1px dashed #e8e8e8').append($link);

			var $hidden = $('<input>').attr('type', 'hidden').attr('name','attachment').val($div.html());
			$('#hj-wall').find('.hj-wall-form-attachment').empty().append($div).append($hidden);
		} else {
			return false;
		}
	}

	hj.wall.base.IsValidImageUrl = function(url, callback) {
		var img = new Image();
		img.onerror = function() { callback(url, false); }
		img.onload =  function() { callback(url, true); }
		img.src = url
	}
	
	hj.wall.base.formSubmit = function(event) {
		event.preventDefault();

		var form = $(this);

		elgg.system_message(elgg.echo('hj:framework:processing'));
		var params = ({
			beforeSubmit : function() {
				if (form.find('input[type="file"]').val() &&
					!form.find('input[name="attachment"]').val()) {
					elgg.register_error(elgg.echo('hj:wall:filehasntuploaded'));
					return false;
				} else {
					return true;
				}
			},
			dataType : 'json',
			success : function(output) {
				if (window.hjdata.lists['elgg-river-main']) {
					hj.framework.ajax.base.listRefresh("elgg-river-main");
				}
				if (window.hjdata.lists['hj-list-wall']) {
					hj.framework.ajax.base.listRefresh("hj-list-wall");
				}
				form.clearForm();
				form.find('.hj-wall-form-attachment').empty();
				form.find('input[type="file"]').val('');
				form.find('ul#hj-wall-tags').empty();
				elgg.system_message(elgg.echo('hj:framework:success'));
				elgg.trigger_hook('success', 'hj:framework:ajax');

			}
		});

		if ($(this).children('input[type="file"]').length > 0) {
			params.iframe = true;
		} else {
			params.iframe = false;
		}

		$(this).ajaxSubmit(params);
	}

	hj.wall.base.uploadFile = function() {

		//$(this).attr('disabled', 'disabled');

		event.preventDefault();

		var form = $(this).closest('form');

		elgg.system_message(elgg.echo('hj:framework:processing'));

		var params = ({
			url : elgg.normalize_url('action/wall/upload'),
			dataType : 'json',
			success : function(output) {
				$('.hj-wall-ajax-loader').hide();

				var $div = $('<div>').css('padding', '10px').css('border-bottom', '1px dashed #e8e8e8').append(output.output.data);
				form.find('.hj-wall-form-attachment').html($div);
				form.find('input[type="submit"]').removeClass('hidden');
				elgg.trigger_hook('success', 'hj:framework:ajax');
				elgg.system_message(elgg.echo('hj:framework:success'));
			},
			iframe : true
		});

		form.ajaxSubmit(params);

	}

	elgg.register_hook_handler('init', 'system', hj.wall.base.init);
	elgg.register_hook_handler('success', 'hj:framework:ajax', hj.wall.base.init);

<?php if (FALSE) : ?></script><?php endif; ?>