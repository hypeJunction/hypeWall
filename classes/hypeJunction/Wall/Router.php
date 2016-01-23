<?php

namespace hypeJunction\Wall;

/**
 * Routing and page handling service
 */
class Router {

	/**
	 * Config
	 * @var Config
	 */
	private $config;

	/**
	 * Constructor
	 *
	 * @param Config $config
	 */
	public function __construct(Config $config) {
		$this->config = $config;
	}

	/**
	 * Handles embedded URLs
	 *
	 * @param array $segments URL segments
	 * @return boolean
	 */
	public function handlePages($segments) {

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
				$post = get_entity($guid);
				if (!$post || !in_array($post->getSubtype(), get_wall_subtypes())) {
					return false;
				}
				$target_guid = $post->getContainerGUID();
				$post_guids = array($post->guid);
				break;
		}

		echo elgg_view('resources/wall', array(
			'target_guid' => $target_guid,
			'post_guids' => $post_guids,
		));
		return true;
	}

	/**
	 * Returns page handler ID
	 * @return string
	 */
	public function getPageHandlerId() {
		return hypeWall()->config->get('pagehandler_id');
	}

	/**
	 * Prefixes the URL with the page handler ID and normalizes it
	 *
	 * @param mixed $url   URL as string or array of segments
	 * @param array $query Query params to add to the URL
	 * @return string
	 */
	public function normalize($url = '', $query = array()) {

		if (is_array($url)) {
			$url = implode('/', $url);
		}

		$url = implode('/', array($this->getPageHandlerId(), $url));

		if (!empty($query)) {
			$url = elgg_http_add_url_query_elements($url, $query);
		}

		return elgg_normalize_url($url);
	}

}
