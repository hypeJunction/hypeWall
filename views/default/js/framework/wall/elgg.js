require(['jquery', 'elgg'], function ($) {
	if ($('.wall-container').length) {
		require(['framework/wall/lib'], function (wall) {
			wall.init();
		});
	}
	$(document).ajaxSuccess(function (event, response, settings) {
		if ($(response.responseText).has('.wall-container')) {
			require(['framework/wall/lib'], function (wall) {
				wall.init();
			});
		}
	});
});