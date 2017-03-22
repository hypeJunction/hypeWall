<?php

namespace hypeJunction\Wall;

use ElggMenuItem;
use ElggRiverItem;

class Menus {

	/**
	 * Add actions to the wall post menu
	 *
	 * @param string $hook   Equals 'register'
	 * @param string $type   Equals 'menu:entity'
	 * @param array  $return Current menu
	 * @param array  $params Additional params
	 * @return array Updated menu
	 */
	public static function entityMenuSetup($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if (!$entity instanceof Post) {
			return $return;
		}

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
						'name' => 'edit',
						'text' => elgg_echo('edit'),
						'title' => elgg_echo('wall:edit'),
						'priority' => 800,
						'href' => "wall/edit/$entity->guid",
			));
		}

		if ($entity->canDelete()) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'delete',
						'text' => elgg_view_icon('delete'),
						'title' => elgg_echo('wall:delete'),
						'priority' => 900,
						'href' => "action/entity/delete?guid=$entity->guid",
						'is_action' => true,
						'confirm' => true,
			));
		}

		return $return;
	}

	/**
	 * Allow users to delete and remove tags from the river
	 *
	 * @param string $hook   Equals 'register'
	 * @param string $type   Equals 'menu:river'
	 * @param array  $return Current menu
	 * @param array  $params Additional params
	 * @return array Updated menu
	 */
	public static function riverMenuSetup($hook, $type, $return, $params) {

		$item = elgg_extract('item', $params);

		if (!($item instanceof ElggRiverItem)) {
			return $return;
		}

		$object = $item->getObjectEntity();

		if (!$object instanceof Post) {
			return;
		}

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
						'name' => 'edit',
						'text' => elgg_echo('edit'),
						'title' => elgg_echo('wall:edit'),
						'priority' => 800,
						'href' => "wall/edit/$object->guid",
			));
		}

		if ($object->canDelete()) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'delete',
						'text' => elgg_view_icon('delete'),
						'title' => elgg_echo('wall:delete'),
						'priority' => 900,
						'href' => "action/entity/delete?guid=$object->guid",
						'is_action' => true,
						'confirm' => true,
			));
		}

		return $return;
	}

	/**
	 * Setup owner block menu
	 *
	 * @param string $hook   Equals 'register'
	 * @param string $type   Equals 'menu:owner_block'
	 * @param array  $return Current menu
	 * @param array  $params Additional params
	 * @return array Updated menu
	 */
	public static function ownerBlockMenuSetup($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if (elgg_instanceof($entity, 'user')) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'wall',
						'text' => elgg_echo('wall'),
						'href' => "wall/owner/{$entity->username}",
			));
		} else if (elgg_instanceof($entity, 'group') && $entity->wall_enable == 'yes') {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'wall',
						'text' => elgg_echo('wall:groups'),
						'href' => "wall/group/{$entity->guid}",
			));
		}

		return $return;
	}

	/**
	 * Add a shortcut link to the user hover menu
	 *
	 * @param string $hook   Equals 'register'
	 * @param string $type   Equals 'menu:user_hover'
	 * @param array  $return Current menu
	 * @param array  $params Additional params
	 * @return array Updated menu
	 */
	public static function userHoverMenuSetup($hook, $type, $return, $params) {
		$entity = elgg_extract('entity', $params);

		if (elgg_instanceof($entity, 'user')) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'wall',
						'text' => ($entity->canWriteToContainer(0, 'object', Post::SUBTYPE)) ? elgg_echo('wall:write') : elgg_echo('wall:view'),
						'href' => "wall/owner/{$entity->username}",
			));
		}
		return $return;
	}

	/**
	 * Setup menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:scraper:card"
	 * @param ElggMenuItem[] $return Menu
	 * @param array          $params Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupCardMenu($hook, $type, $return, $params) {

		$user = elgg_get_logged_in_user_entity();
		if (!$user) {
			return;
		}

		$href = elgg_extract('href', $params);
		if (!$href) {
			return;
		}

		$return[] = ElggMenuItem::factory([
			'name' => 'repost',
			'href' => elgg_http_add_url_query_elements("wall/owner/$user->username", [
				'address' => $href,
			]),
			'text' => elgg_view_icon('retweet'),
			'title' => elgg_echo('wall:repost'),
		]);

		return $return;
	}
}
