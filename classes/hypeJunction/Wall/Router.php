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

		elgg_push_breadcrumb(elgg_echo('wall'), $this->getPageHandlerId());

		switch ($segments[0]) {
			default :
				$user = elgg_get_logged_in_user_entity();
				forward($this->normalize(array('owner', $user->username)));
				break;

			case 'user' :
			case 'owner' :
				echo elgg_view('resources/wall/owner', array(
					'username' => elgg_extract(1, $segments),
					'post_guid' => elgg_extract(2, $segments),
				));
				return true;

			case 'post' :

				$guid = elgg_extract(1, $page);

				elgg_entity_gatekeeper($guid, 'object');

				$post = get_entity($guid);
				forward($post->getURL());
				break;

			case 'group' :
			case 'container' :
				$guid = elgg_extract(1, $page);

				elgg_entity_gatekeeper($guid);

				$group = get_entity($guid);

				elgg_set_page_owner_guid($group->guid);

				$name = elgg_instanceof($group, 'object') ? $group->title : $group->name;
				$title = elgg_echo('wall:owner', array($name));
				elgg_push_breadcrumb($title, $this->normalize($page[0], $group->guid));

				if (isset($page[2])) {
					elgg_entity_gatekeeper($page[2]);
					$post = get_entity($page[2]);
				}

				$content = elgg_view("framework/wall/group", array(
					'post' => $post,
				));

				$layout = elgg_view_layout('content', array(
					'title' => $title,
					'content' => $content,
					'filter' => false,
				));
				echo elgg_view_page($title, $layout);
				return true;
		}

		return false;
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
