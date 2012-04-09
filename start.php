<?php

/* hypeWall
 *
 * @package hypeJunction
 * @subpackage hypeWall
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyrigh (c) 2012, Ismayil Khayredinov
 */

elgg_register_event_handler('init', 'system', 'hj_wall_init', 505);

function hj_wall_init() {

	$plugin = 'hypeWall';

	if (!elgg_is_active_plugin('hypeFramework')) {
		register_error(elgg_echo('hj:framework:disabled', array($plugin, $plugin)));
		disable_plugin($plugin);
	}

	$shortcuts = hj_framework_path_shortcuts($plugin);

	// Register Libraries
	elgg_register_library('hj:wall:base', $shortcuts['lib'] . 'wall/base.php');
	elgg_load_library('hj:wall:base');

	$js = elgg_get_simplecache_url('js', 'hj/wall/base');
	elgg_register_js('hj.wall.base', $js);
	$js = elgg_get_simplecache_url('js', 'hj/wall/oembed_init');
	elgg_register_js('hj.wall.oembed_init', $js);

	elgg_register_js('jquery.oembed', 'mod/hypeWall/vendors/jquery.oembed/jquery.oembed.js');

	elgg_load_js('jquery.oembed');
	elgg_load_js('hj.wall.oembed_init');

	$css = elgg_get_simplecache_url('css', 'hj/wall/base');
	elgg_register_css('hj.wall.base', $css);

	elgg_register_css('jquery.oembed', 'mod/hypeWall/vendors/jquery.oembed/jquery.oembed.css');

	elgg_register_action('wall/status', $shortcuts['actions'] . 'wall/status.php');
	elgg_register_action('wall/post', $shortcuts['actions'] . 'wall/post.php');
	elgg_register_action('wall/upload', $shortcuts['actions'] . 'wall/upload.php');
	elgg_register_action('wall/photo', $shortcuts['actions'] . 'wall/photo.php');
	elgg_register_action('wall/file', $shortcuts['actions'] . 'wall/file.php');

	elgg_extend_view('core/river/filter', 'hj/wall/container', 1);

	elgg_register_page_handler('wall', 'hj_wall_page_handler');
	elgg_register_entity_url_handler('object', 'hjwall', 'hj_wall_url_handler');

	elgg_register_plugin_hook_handler('permissions_check', 'object', 'hj_wall_owner_edit_permissions');

	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'hj_wall_owner_block_menu');
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'hj_wall_user_hover_menu');

	elgg_register_widget_type('wall', elgg_echo('hj:wall'), elgg_echo('hj:wall:widget:description'));
}

function hj_wall_url_handler($entity) {
	return "wall/post/$entity->guid";
}

function hj_wall_page_handler($page) {

	$user = elgg_get_logged_in_user_entity();
	elgg_push_breadcrumb(elgg_echo('hj:wall'), "wall/user/$user->guid");

	$path = elgg_get_plugins_path() . 'hypeWall/pages/';
	switch ($page[0]) {
		default :
			forward("wall/user/$user->username");
			break;

		case 'user' :
			if (!$page[1]) {
				return false;
			}
			$owner = get_user_by_username($page[1]);
			set_input('username', $page[1]);
			elgg_set_page_owner_guid($owner->guid);
			include "{$path}user.php";
			return true;
			break;

		case 'post' :
			if (!$page[1]) {
				return false;
			}
			set_input('guid', $page[1]);
			include "{$path}post.php";
			return true;
			break;
	}

	return false;
}

function hj_wall_owner_edit_permissions($hook, $type, $return, $params) {

	$entity = $params['entity'];
	$user = $params['user'];

	if (!elgg_instanceof($entity) || !elgg_instanceof($user)) {
		return $return;
	}
	if ($entity->getSubtype() == 'hjwall' && check_entity_relationship($user->guid, 'wall_owner', $entity->guid)) {
		return true;
	}

	return $return;
}

function hj_wall_owner_block_menu($hook, $type, $return, $params) {

	if (elgg_instanceof($params['entity'], 'user')) {
		$menu_item = array(
			'name' => 'wall',
			'text' => elgg_echo('hj:wall'),
			'href' => "wall/user/{$params['entity']->username}"
		);
		$return[] = ElggMenuItem::factory($menu_item);
	}

	return $return;
}

function hj_wall_user_hover_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$menu_item = array(
			'name' => 'wall',
			'text' => elgg_echo('hj:wall:write'),
			'href' => "wall/user/{$params['entity']->username}"
		);
		$return[] = ElggMenuItem::factory($menu_item);
	}
	return $return;
}