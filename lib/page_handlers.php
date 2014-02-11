<?php

namespace hypeJunction\Wall;

/**
 * Give wall posts their own URL
 *
 * @param ElggObject $entity
 * @return string
 */
function url_handler($entity) {
	return elgg_normalize_url(PAGEHANDLER . '/post/' . $entity->getOwnerEntity()->username . '/' . $entity->guid);
}

/**
 * Handler walls and posts
 *
 * User wall:	wall/owner/<username>
 * Post:		wall/post/<guid>
 *
 * @param array $page
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
			if (!$owner) {
				return false;
			}

			elgg_set_page_owner_guid($owner->guid);

			$title = elgg_echo('wall:owner', array($owner->name));
			elgg_push_breadcrumb($title, PAGEHANDLER . "/owner/$owner->username");

			if (isset($page[1])) {
				$guid = get_input('guid');
				$post = get_entity($guid);
			}

			if (elgg_instanceof($post, 'object', 'hjwall')) {
				$post_owner = $post->getOwnerentity();
				$title = elgg_echo('wall:post:owner', array($post_owner->name));
				elgg_push_breadcrumb($title);
				$content = elgg_view_entity($post, array(
					'full_view' => true
				));
			} else {
				$content = elgg_view("framework/wall/owner");
			}

			$layout = elgg_view_layout('content', array(
				'title' => $title,
				'content' => $content,
				'filter' => false,
			));
			echo elgg_view_page($title, $layout);
			return true;
			break;

		case 'post' :
			$guid = get_input('guid');
			$post = get_entity($guid);

			if (!elgg_instanceof($post, 'object', 'hjwall')) {
				return false;
			}

			$owner = $post->getOwnerEntity();
			forward(PAGEHANDLER . "/owner/$owner->username/$post->guid");
			break;
	}

	return false;
}
