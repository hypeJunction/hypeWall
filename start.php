<?php

/**
 * User Walls
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 */
require_once __DIR__ . '/autoloader.php';

use hypeJunction\Wall\Menus;
use hypeJunction\Wall\Permissions;
use hypeJunction\Wall\Post;
use hypeJunction\Wall\Router;
use hypeJunction\Wall\Notifications;

elgg_register_event_handler('init', 'system', function() {

	elgg_register_page_handler('wall', [Router::class, 'handleWallPages']);
	elgg_register_plugin_hook_handler('entity:url', 'object', [Router::class, 'setEntityUrls']);

	elgg_register_entity_type('object', Post::SUBTYPE);

	elgg_extend_view('elgg.css', 'framework/wall/stylesheet.css');

	// AJAX view to load URL previews
	elgg_register_ajax_view('output/wall/url');

	// Display wall form
	elgg_extend_view('page/layouts/elements/filter', 'framework/wall/container', 100);

	// Notifications
	elgg_register_event_handler('publish', 'object', [Notifications::class, 'sendCustomNotifications']);

	elgg_register_notification_event('object', 'hjwall', ['publish']);
	elgg_register_plugin_hook_handler('prepare', 'notification:publish:object:hjwall', [Notifications::class, 'formatMessage']);

	elgg_register_plugin_hook_handler('likes:is_likable', 'object:hjwall', 'Elgg\Values::getTrue');

	elgg_register_action('wall/status', __DIR__ . '/actions/wall/status.php');
	elgg_register_action('wall/remove_tag', __DIR__ . '/actions/wall/remove_tag.php');
	elgg_register_action('wall/geopositioning/update', __DIR__ . '/actions/wall/geopositioning/update.php', 'public');

	elgg_register_plugin_hook_handler('container_permissions_check', 'object', [Permissions::class, 'containerPermissionsCheck']);

	elgg_register_plugin_hook_handler('register', 'menu:river', [Menus::class, 'riverMenuSetup']);
	elgg_register_plugin_hook_handler('register', 'menu:entity', [Menus::class, 'entityMenuSetup']);
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', [Menus::class, 'ownerBlockMenuSetup']);
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', [Menus::class, 'userHoverMenuSetup']);
	elgg_register_plugin_hook_handler('register', 'menu:scraper:card', [Menus::class, 'setupCardMenu']);
	
	elgg_register_widget_type('wall', elgg_echo('wall'), elgg_echo('wall:widget:description'));

	add_group_tool_option('wall', elgg_echo('wall:groups:enable'), false);
	elgg_extend_view('groups/tool_latest', 'framework/wall/group_module');

	// Export
	$subtype = Post::SUBTYPE;
	elgg_register_plugin_hook_handler('aliases', 'graph', [Post::class, 'getGraphAlias']);
	elgg_register_plugin_hook_handler('graph:properties', "object:$subtype", [Post::class, 'getPostProperties']);
});

elgg_register_event_handler('upgrade', 'system', function() {
	require_once __DIR__ . '/lib/upgrades.php';
});