<?php

/**
 * User Walls
 *
 * @package hypeJunction
 * @subpackage Wall
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 */

namespace hypeJunction\Wall;

const PLUGIN_ID = 'hypeWall';
const PAGEHANDLER = 'wall';

require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/hooks.php';
require_once __DIR__ . '/lib/page_handlers.php';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');
elgg_register_event_handler('upgrade', 'system', __NAMESPACE__ . '\\upgrade');

function init() {

	/**
	 * Handle pages and URLs
	 */
	elgg_register_page_handler(PAGEHANDLER, __NAMESPACE__ . '\\page_handler');
	elgg_register_entity_url_handler('object', 'hjwall', __NAMESPACE__ . '\\url_handler');

	/**
	 * Add wall posts to search
	 */
	elgg_register_entity_type('object', 'hjwall');

	/**
	 * JS, CSS and Views
	 */
	elgg_register_simplecache_view('css/framework/wall');
	elgg_register_css('wall', elgg_get_simplecache_url('css', 'framework/wall/css'));

	elgg_register_simplecache_view('js/framework/wall/base');
	elgg_register_js('wall.status', elgg_get_simplecache_url('js', 'framework/wall/status'), 'footer');

	elgg_register_js('jquery.filedrop.js', '/mod/' . PLUGIN_ID . '/vendors/filedrop/jquery.filedrop.js', 'footer');

	elgg_register_simplecache_view('js/framework/wall/filedrop');
	elgg_register_js('wall.filedrop', elgg_get_simplecache_url('js', 'framework/wall/filedrop'), 'footer');

	// Display wall form
	elgg_extend_view('page/layouts/content/filter', 'framework/wall/container', 100);

	// Load FontAwesome
	elgg_extend_view('page/elements/head', 'framework/fonts/font-awesome');

	// Add User Location to config
	elgg_extend_view('js/initialize_elgg', 'js/framework/wall/config');

	// AJAX view to load URL previews
	elgg_register_ajax_view('output/wall/url');

	/**
	 * Register actions
	 */
	$actions_path = __DIR__ . '/actions/';
	elgg_register_action('wall/status', $actions_path . 'wall/status.php');
	elgg_register_action('wall/photo', $actions_path . 'wall/photo.php');
	elgg_register_action('wall/file', $actions_path . 'wall/file.php');
	elgg_register_action('wall/content', $actions_path . 'wall/content.php');
	elgg_register_action('wall/url', $actions_path . 'wall/url.php');

	elgg_register_action('wall/upload', $actions_path . 'wall/upload.php');

	elgg_register_action('wall/delete', $actions_path . 'wall/delete.php');
	elgg_register_action('wall/remove_tag', $actions_path . 'wall/remove_tag.php');

	elgg_register_action('wall/geopositioning/update', $actions_path . 'wall/geopositioning/update.php', 'public');

	/**
	 * Register hooks
	 */
	elgg_register_plugin_hook_handler('permissions_check', 'object', __NAMESPACE__ . '\\permissions_check');
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', __NAMESPACE__ . '\\container_permissions_check');

	elgg_register_plugin_hook_handler('register', 'menu:river', __NAMESPACE__ . '\\river_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:entity', __NAMESPACE__ . '\\entity_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', __NAMESPACE__ . '\\owner_block_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', __NAMESPACE__ . '\\user_hover_menu_setup');

	elgg_register_widget_type('wall', elgg_echo('wall'), elgg_echo('wall:widget:description'));

	elgg_register_plugin_hook_handler('get_views', 'ecml', __NAMESPACE__ . '\\get_ecml_views');
}

/**
 * Run upgrade scripts
 */
function upgrade() {
	include_once __DIR__ . '/lib/upgrades.php';
}
