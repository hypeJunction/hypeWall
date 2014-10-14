<?php

namespace hypeJunction\Wall;

/**
 * Handler walls and posts
 *
 * User wall:	wall/owner/<username>
 * Post:		wall/post/<guid>
 *
 * @param array $page URL segments
 * @return boolean
 */
function page_handler($page) {

	elgg_push_breadcrumb(elgg_echo('wall'), PAGEHANDLER);

	switch ($page[0]) {
		default :
			$user = elgg_get_logged_in_user_entity();
			forward(PAGEHANDLER . "/owner/$user->username");
			break;

		case 'user' :
		case 'owner' :
			$username = elgg_extract(1, $page);
			$owner = get_user_by_username($username);

			elgg_entity_gatekeeper($owner->guid, 'user');

			elgg_set_page_owner_guid($owner->guid);

			$title = elgg_echo('wall:owner', array($owner->name));
			elgg_push_breadcrumb($title, PAGEHANDLER . "/owner/$owner->username");

			if (isset($page[2])) {
				elgg_entity_gatekeeper($page[2]);
				$post = get_entity($page[2]);
			}

			$content = elgg_view("framework/wall/owner", array(
				'post' => $post
			));

			$layout = elgg_view_layout('content', array(
				'title' => $title,
				'content' => $content,
				'filter' => false,
			));
			echo elgg_view_page($title, $layout);
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
			elgg_push_breadcrumb($title, implode('/', array(PAGEHANDLER, $page[0], $group->guid)));

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
