<?php

namespace hypeJunction\Wall;

use ElggMenuItem;
use ElggRiverItem;

/**
 * Allow users to post on each other's walls
 * Container here is the wall, and can be a user or group
 * @param type $hook
 * @param type $type
 * @param type $return
 * @param type $params
 * @return boolean
 */
function container_permissions_check($hook, $type, $return, $params) {
	$container = elgg_extract('container', $params);
	$user = elgg_extract('user', $params);
	$subtype = elgg_extract('subtype', $params);

	if ($subtype !== WALL_SUBTYPE) {
		return $return;
	}

	if (elgg_instanceof($container, 'user') && $user) {
		return true;
	}

	return $return;
}

/**
 * Add actions to the wall post menu
 *
 * @param string $hook		Equals 'register'
 * @param string $type		Equals 'menu:entity'
 * @param array $return		Current menu
 * @param array $params		Additional params
 * @return array			Updated menu
 */
function entity_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);

	if (elgg_instanceof($entity, 'object', WALL_SUBTYPE)) {

		$logged_in = elgg_get_logged_in_user_entity();
		if (check_entity_relationship($logged_in->guid, 'tagged_in', $entity->guid)) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'remove_tag',
						'text' => elgg_echo('wall:remove_tag'),
						'title' => elgg_echo('wall:remove_tag'),
						'priority' => 800,
						'href' => "action/wall/remove_tag?guid=$entity->guid",
						'is_action' => true,
			));
		}

		if ($entity->canEdit()) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'delete',
						'text' => elgg_view_icon('delete'),
						'title' => elgg_echo('wall:delete'),
						'priority' => 900,
						'href' => "action/wall/delete?guid=$entity->guid",
						'is_action' => true,
			));
		}
	}

	return $return;
}

/**
 * Allow users to delete and remove tags from the river
 *
 * @param string $hook		Equals 'register'
 * @param string $type		Equals 'menu:river'
 * @param array $return		Current menu
 * @param array $params		Additional params
 * @return array			Updated menu
 */
function river_menu_setup($hook, $type, $return, $params) {

	$item = elgg_extract('item', $params);

	if (!($item instanceof ElggRiverItem)) {
		return $return;
	}

	$object = $item->getObjectEntity();

	if (elgg_instanceof($object, 'object', WALL_SUBTYPE)) {

		$logged_in = elgg_get_logged_in_user_entity();
		if (check_entity_relationship($logged_in->guid, 'tagged_in', $object->guid)) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'remove_tag',
						'text' => elgg_echo('wall:remove_tag'),
						'title' => elgg_echo('wall:remove_tag'),
						'priority' => 800,
						'href' => "action/wall/remove_tag?guid=$object->guid",
						'is_action' => true,
			));
		}

		if ($object->canEdit()) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'delete',
						'text' => elgg_view_icon('delete'),
						'title' => elgg_echo('wall:delete'),
						'priority' => 900,
						'href' => "action/wall/delete?guid=$object->guid",
						'is_action' => true,
			));
		}
	}

	return $return;
}

/**
 * Setup owner block menu
 *
 * @param string $hook		Equals 'register'
 * @param string $type		Equals 'menu:owner_block'
 * @param array $return		Current menu
 * @param array $params		Additional params
 * @return array			Updated menu
 */
function owner_block_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);

	if (elgg_instanceof($entity, 'user')) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'wall',
					'text' => elgg_echo('wall'),
					'href' => PAGEHANDLER . "/owner/{$entity->username}"
		));
	}

	return $return;
}

/**
 * Add a shortcut link to the user hover menu
 *
 * @param string $hook		Equals 'register'
 * @param string $type		Equals 'menu:user_hover'
 * @param array $return		Current menu
 * @param array $params		Additional params
 * @return array			Updated menu
 */
function user_hover_menu_setup($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params);

	if (elgg_instanceof($entity, 'user')) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'wall',
					'text' => elgg_echo('wall:write'),
					'href' => PAGEHANDLER . "/owner/{$entity->username}",
		));
	}
	return $return;
}

/**
 * Add views in which ECML should be rendered
 *
 * @param string $hook		Equals 'get_views'
 * @param string $type		Equals 'ecml'
 * @param array $views		Current list of views
 * @param array $params		Additional params
 * @return array			Updated lsit of views
 */
function get_ecml_views($hook, $type, $views, $params) {
	$views['output/wall/url'] = elgg_echo('wall:ecml:url');
	$views['output/wall/attachment'] = elgg_echo('wall:ecml:attachment');
	$views['river/elements/layout'] = elgg_echo('wall:ecml:river');
	return $views;
}

/**
 * Hijack wire views to display more meaningful content
 *
 * @param string $hook	Equals 'view'
 * @param string $type	Equals 'object/thewire'
 * @param string $return HTML
 * @param array $params  Additional params
 * @uses $params['vars']
 * @return string
 */
function hijack_wire($hook, $type, $return, $params) {

	$vars = elgg_extract('vars', $params);
	$entity = elgg_extract('entity', $vars);

	if ($entity->method == 'wall') {
		return elgg_view('object/hjwall', $vars);
	}

	return $return;
}