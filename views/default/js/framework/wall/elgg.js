require(['jquery', 'elgg'], function ($) {

	if ($('.wall-container').length) {
		require(['framework/wall/lib'], function (wall) {
			wall.init();
		});
	}

	$(document).ajaxSuccess(function (event, response, settings) {
		var data = '';
		if (settings.dataType === 'json') {
			data = $.parseJSON(response.responseText);
		} else if (settings.dataType === 'html') {
			data = response.resopnseText;
		}
		if ($(data).has('.wall-container')) {
			require(['framework/wall/lib'], function (wall) {
				wall.init();
			});
		}
	});
});