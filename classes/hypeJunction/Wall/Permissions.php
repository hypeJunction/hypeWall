<?php

namespace hypeJunction\Wall;

use ElggUser;

/**
 * @access private
 */
class Permissions {

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
	public static function containerPermissionsCheck($hook, $type, $return, $params) {
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

}
