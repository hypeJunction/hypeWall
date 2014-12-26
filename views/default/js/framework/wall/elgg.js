require(['jquery', 'elgg'], function ($) {
	if ($('.wall-container').length) {
		require(['framework/wall/lib'], function (wall) {
			wall.init();
		});
	}
	$(document).ajaxSuccess(function (data) {
		if ($(data).has('.wall-container')) {
			require(['framework/wall/lib'], function (wall) {
				wall.init();
			});
		}
	});
});