<?php

namespace hypeJunction\Wall;

use ElggMenuItem;
use ElggRiverItem;
use ElggUser;
use hypeJunction\Scraper\Models\Resources;
use hypeJunction\Scraper\Services\Extractor;

/**
 * Plugin hooks service
 */
class HookHandlers {

	private $config;
	private $router;

	/**
	 * Constructor
	 * @param Config $config
	 * @param Router $router
	 * @param Resources $resources
	 * @param Extractor $extractor
	 */
	public function __construct(Config $config, Router $router) {
		$this->config = $config;
		$this->router = $router;
	}

	/**
	 * Give wall posts their own URL
	 *
	 * @param string $hook   Equals 'entity:url'
	 * @param string $type   Equals 'object'
	 * @param string $return Current URL
	 * @param array  $params Additional params
	 * @return string Filtered URL
	 */
	public function urlHandler($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if ($entity instanceof Post && $entity->getSubtype() == Post::SUBTYPE) {
			$container = $entity->getContainerEntity();
			if (elgg_instanceof($container, 'group')) {
				return $this->router->normalize(array('group', $container->guid, $entity->guid));
			} else if (elgg_instanceof($container, 'user')) {
				return $this->router->normalize(array('owner', $container->username, $entity->guid));
			}
		}

		return $return;
	}

	/**
	 * Allow users to post on each other's walls
	 * Container here is the wall, and can be a user or group
	 *
	 * @param string  $hook   Equals 'container_permissions_check'
	 * @param string  $type   Equals 'object'
	 * @param boolean $return Current permission
	 * @param array   $params Additional params
	 * @return boolean Filtered permission
	 */
	public function containerPermissionsCheck($hook, $type, $return, $params) {
		$container = elgg_extract('container', $params);
		$user = elgg_extract('user', $params);
		$subtype = elgg_extract('subtype', $params);

		if ($subtype !== Post::SUBTYPE) {
			return $return;
		}

		if (!$container instanceof ElggUser) {
			return $return;
		}

		if (!$user instanceof ElggUser) {
			return $return;
		}

		if ($container->isFriend($user)) {
			return true;
		} else {
			$third_party_wall_global = elgg_get_plugin_setting('third_party_wall', 'hypeWall');
			$third_party_wall_user = elgg_get_plugin_user_setting('third_party_wall', $container->guid, 'hypeWall');

			if ($third_party_wall_global && $third_party_wall_user) {
				return true;
			}
		}

		return $return;
	}

	/**
	 * Add actions to the wall post menu
	 *
	 * @param string $hook   Equals 'register'
	 * @param string $type   Equals 'menu:entity'
	 * @param array  $return Current menu
	 * @param array  $params Additional params
	 * @return array Updated menu
	 */
	public function entityMenuSetup($hook, $type, $return, $params) {

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
			if (elgg_instanceof($entity, 'object', 'hjwall')) {
				$action = "action/wall/delete?guid=$entity->guid";
			} else if (elgg_instanceof($entity, 'object', 'thewire')) {
				$action = "action/thewire/delete?guid=$entity->guid";
			}
			if ($action) {
				$return[] = ElggMenuItem::factory(array(
							'name' => 'delete',
							'text' => elgg_view_icon('delete'),
							'title' => elgg_echo('wall:delete'),
							'priority' => 900,
							'href' => $action,
							'is_action' => true,
							'confirm' => true,
				));
			}
		}

		if ($params['handler'] == 'wall') {
			foreach ($return as $key => $item) {
				if ($item instanceof ElggMenuItem && $item->getName() == 'edit') {
					unset($return[$key]);
				}
			}
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
	public function riverMenuSetup($hook, $type, $return, $params) {

		$item = elgg_extract('item', $params);

		if (!($item instanceof ElggRiverItem)) {
			return $return;
		}

		$object = $item->getObjectEntity();

		if (!$object instanceof Post) {

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
		}

		if ($object->canEdit()) {
			if (elgg_instanceof($object, 'object', 'hjwall')) {
				$action = "action/wall/delete?guid=$object->guid";
			} else if (elgg_instanceof($object, 'object', 'thewire')) {
				$action = "action/thewire/delete?guid=$object->guid";
			}
			if ($action) {
				$return[] = ElggMenuItem::factory(array(
							'name' => 'delete',
							'text' => elgg_view_icon('delete'),
							'title' => elgg_echo('wall:delete'),
							'priority' => 900,
							'href' => $action,
							'is_action' => true,
							'confirm' => true,
				));
			}
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
	public function ownerBlockMenuSetup($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if (elgg_instanceof($entity, 'user')) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'wall',
						'text' => elgg_echo('wall'),
						'href' => $this->router->normalize("owner/{$entity->username}"),
			));
		} else if (elgg_instanceof($entity, 'group') && $entity->wall_enable == 'yes') {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'wall',
						'text' => elgg_echo('wall:groups'),
						'href' => $this->router->normalize("group/{$entity->guid}"),
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
	public function userHoverMenuSetup($hook, $type, $return, $params) {
		$entity = elgg_extract('entity', $params);

		if (elgg_instanceof($entity, 'user')) {
			$return[] = ElggMenuItem::factory(array(
						'name' => 'wall',
						'text' => ($entity->canWriteToContainer(0, 'object', Post::SUBTYPE)) ? elgg_echo('wall:write') : elgg_echo('wall:view'),
						'href' => $this->router->normalize("owner/{$entity->username}"),
			));
		}
		return $return;
	}

	/**
	 * Add views in which ECML should be rendered
	 *
	 * @param string $hook   Equals 'get_views'
	 * @param string $type   Equals 'ecml'
	 * @param array  $views  Current list of views
	 * @param array  $params Additional params
	 * @return array Updated lsit of views
	 */
	public function getECMLViews($hook, $type, $views, $params) {
		$views['output/wall/url'] = elgg_echo('wall:ecml:url');
		$views['output/wall/attachment'] = elgg_echo('wall:ecml:attachment');
		$views['river/elements/layout'] = elgg_echo('wall:ecml:river');
		return $views;
	}

	/**
	 * Hijack wire views to display more meaningful content
	 *
	 * @param string $hook   Equals 'view'
	 * @param string $type   Equals 'object/thewire'
	 * @param string $return HTML
	 * @param array  $params Additional params
	 * @uses $params['vars']
	 * @return string
	 */
	public function hijackWire($hook, $type, $return, $params) {

		$vars = elgg_extract('vars', $params);
		$entity = elgg_extract('entity', $vars);

		if ($entity->method == 'wall') {
			if (elgg_in_context('thewire')) {
				$return .= elgg_view('object/thewire/extras', $vars);
			} else {
				$return = elgg_view('object/hjwall', $vars);
			}
		}

		return $return;
	}

	/**
	 * Hijack wire river views to display more meaningful content
	 *
	 * @param string $hook   Equals 'view'
	 * @param string $type   Equals 'river/object/thewire/create'
	 * @param string $return HTML
	 * @param array  $params Additional params
	 * @uses $params['vars']
	 * @return string
	 */
	public function hijackWireRiver($hook, $type, $return, $params) {

		$vars = elgg_extract('vars', $params);
		$item = elgg_extract('item', $vars);
		if (!$item instanceof ElggRiverItem) {
			return $return;
		}

		$entity = $item->getObjectEntity();
		if ($entity->method == 'wall') {
			return elgg_view('river/object/hjwall/create', $vars);
		}

		return $return;
	}

	public function getGraphAlias($hook, $type, $return, $params) {
		$return['object'][Post::SUBTYPE] = ':wall';
		return $return;
	}

	public function getPostProperties($hook, $type, $return, $params) {

		$fields[] = 'location';
						$fields[] = 'address';
						$fields[] = 'tagged_users';
						$fields[] = 'attachments';

		$return[] = new \hypeJunction\Data\Property('location', array(
			'getter' => '\hypeJunction\Data\Values::getLocation',
			'setter' => '\hypeJunction\Data\Values::setLocation',
			'type' => 'string',
			'input' => 'location',
			'output' => 'location',
			'validation' => array(
				'rules' => array(
					'type' => 'location',
				)
			)
		));

		$return[] = new \hypeJunction\Data\Property('address', array(
			'getter' => '\hypeJunction\Data\Values::getVerbatim',
			'setter' => '\hypeJunction\Data\Values::setVerbatim',
			'type' => 'url',
			'input' => 'url',
			'output' => 'url',
			'validation' => array(
				'rules' => array(
					'type' => 'url',
				)
			),
		));

		$return[] = new \hypeJunction\Data\Property('embed', array(
			'attribute' => 'address',
			'getter' => '\hypeJunction\Data\Values::getUrlMetadata',
			'read_only' => true,
		));

		$return[] = new \hypeJunction\Data\Property('tagged_users', array(
			'getter' => '\hypeJunction\Wall\Post::getTaggedUsersProp',
			'read_only' => true,
		));

		$return[] = new \hypeJunction\Data\Property('attachments', array(
			'getter' => '\hypeJunction\Wall\Post::getAttachmentsProp',
			'read_only' => true,
		));

		return $return;
	}
}
