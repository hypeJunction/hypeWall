<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>

	elgg.provide('hj.wall.base');

	hj.wall.base.oembed = function() {
		$(".oembed")
		.not(".obembed_init")
		.oembed(null, {
			embedMethod:'fill',
			maxWidth: 500
		})
		.addClass("oembed_init");
	}

	elgg.register_hook_handler('init', 'system', hj.wall.base.oembed);
	elgg.register_hook_handler('success', 'hj:framework:ajax', hj.wall.base.oembed);

<?php if (FALSE) : ?></script><?php endif; ?>