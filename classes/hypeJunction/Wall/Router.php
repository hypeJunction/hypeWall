<?php

namespace hypeJunction\Wall;

/**
 * Routing and page handling service
 * @access private
 */
class Router {

	/**
	 * Handles embedded URLs
	 *
	 * @param array $segments URL segments
	 * @return boolean
	 */
	public static function handleWallPages($segments) {

		$page = array_shift($segments);
		$target_guid = false;
		$post_guids = (array) get_input('post_guids', array());

		switch ($page) {

			default :
				$target_guid = $page;
				if (!$target_guid) {
					$target_guid = elgg_get_logged_in_user_guid();
				}
				break;

			case 'user' :
			case 'owner' :
				$username = array_shift($segments);
				$user = get_user_by_username($username);
				if ($user) {
					$target_guid = $user->guid;
				}
				$post_guid = array_shift($segments);
				if ($post_guid) {
					$post_guids[] = $post_guid;
				}
				break;

			case 'group' :
			case 'container' :
				$target_guid = array_shift($segments);
				$post_guid = array_shift($segments);
				if ($post_guid) {
					$post_guids[] = $post_guid;
				}
				break;

			case 'post' :
				$guid = array_shift($segments);
				elgg_entity_gatekeeper($guid, 'object', Post::SUBTYPE);

				$post = get_entity($guid);
				$target_guid = $post->getContainerGUID();
				$post_guids = [$post->guid];
				break;

			case 'edit' :
				$guid = array_shift($segments);
				echo elgg_view_resource('wall/edit', [
					'guid' => $guid,
				]);
				return true;
		}

		echo elgg_view('resources/wall', array(
			'target_guid' => $target_guid,
			'post_guids' => $post_guids,
		));

		return true;
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
	public static function setEntityUrls($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);

		if ($entity instanceof Post && $entity->getSubtype() == Post::SUBTYPE) {
			$container = $entity->getContainerEntity();
			if (elgg_instanceof($container, 'group')) {
				return elgg_normalize_url("wall/group/$container->guid/$entity->guid#elgg-object-$entity->guid");
			} else if (elgg_instanceof($container, 'user')) {
				return elgg_normalize_url("wall/owner/$container->username/$entity->guid#elgg-object-$entity->guid");
			}
		}

		return $return;
	}

}
