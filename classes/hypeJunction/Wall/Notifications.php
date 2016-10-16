<?php

namespace hypeJunction\Wall;

use Elgg\Notifications\Notification;
use ElggEntity;
use ElggUser;

/**
 * @access private
 */
class Notifications {

	/**
	 * Prepare a notification for when the wall post or wire is created
	 *
	 * @param string      $hook         "prepare"
	 * @param string      $type         "notification:publish:object:hjwall"
	 * @param Notfication $notification Notification object
	 * @param array       $params       Hook params
	 * @return Notification
	 */
	public static function formatMessage($hook, $type, $notification, $params) {

		$event = elgg_extract('event', $params);
		$entity = $event->getObject();
		$recipient = elgg_extract('recipient', $params);
		$language = elgg_extract('language', $params);
		$method = elgg_extract('method', $params);

		if (!$entity instanceof Post || $entity->origin != 'wall') {
			return $notification;
		}

		$poster = $entity->getOwnerEntity();
		$wall_owner = $entity->getContainerEntity();

		$target = elgg_echo("wall:target:{$entity->getSubtype()}");

		if ($poster->guid == $wall_owner->guid) {
			$ownership = elgg_echo('wall:ownership:own', array($target), $language);
		} else if ($wall_owner->guid == $recipient->guid) {
			$ownership = elgg_echo('wall:ownership:your', array($target), $language);
		} else {
			$ownership = elgg_echo('wall:ownership:owner', array($wall_owner->name, $target), $language);
		}

		$poster_url = elgg_view('output/url', array(
			'text' => $poster->name,
			'href' => $poster->getURL(),
		));

		$ownership_url = elgg_view('output/url', array(
			'text' => $ownership,
			'href' => $entity->getURL(),
		));

		$notification->summary = elgg_echo('wall:new:notification:subject', array($poster_url, $ownership_url), $language);
		$notification->subject = strip_tags($notification->summary);

		$notification->body = elgg_echo('wall:new:notification:message', array(
			$poster_url,
			$ownership_url,
			$entity->formatMessage(true),
			$entity->getURL()
		), $language);

		return $notification;
	}

	/**
	 * Listen to the 'publish','object' event and send out notifications
	 * to interested users, as well as anyone tagged
	 *
	 * @param string     $event       Equals 'publish'
	 * @param string     $entity_type Equals 'object'
	 * @param ElggEntity $entity      Published entity
	 * @return boolean
	 */
	public static function sendCustomNotifications($event, $entity_type, $entity) {

		if (!$entity instanceof Post || $entity->origin !== 'wall') {
			return true;
		}

		$poster = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		$message = $entity->formatMessage(true);

		$sent = array(elgg_get_logged_in_user_guid(), $poster->guid, $container->guid);

		// Notify wall owner
		if ($poster->guid !== $container->guid && $container instanceof ElggUser) {
			$to_guid = $container->guid;
			$from_guid = $poster->guid;

			$language = $container->language;

			$target = elgg_echo("wall:target:{$entity->getSubtype()}", array(), $language);
			$target_url = elgg_view('output/url', array(
				'text' => $target,
				'href' => $entity->getURL(),
			));

			$ownership = elgg_echo('wall:ownership:your', array($target_url), $language);

			$poster_url = elgg_view('output/url', array(
				'text' => $poster->name,
				'href' => $poster->getURL(),
			));
			$summary = elgg_echo('wall:new:notification:subject', array($poster_url, $ownership), $language);
			$subject = strip_tags($summary);
			$body = elgg_echo('wall:new:notification:message', array(
				$poster_url,
				$ownership,
				$message,
				$entity->getURL()
			), $language);

			notify_user($to_guid, $from_guid, $subject, $body, array(
				'summary' => $summary,
				'object' => $entity,
				'action' => 'received',
			));
		}

		// Notify tagged users
		$tagged_friends = $entity->getTaggedFriends();
		foreach ($tagged_friends as $tagged_friend) {
			// user tagged herself or the wall owner
			if ($tagged_friend->guid == $poster->guid || $tagged_friend->guid == $container->guid || in_array($tagged_friend->guid, $sent)) {
				continue;
			}

			$language = $tagged_friend->language;

			$sent[] = $tagged_friend->guid;

			$to_guid = $tagged_friend->guid;
			$from_guid = $poster->guid;

			$poster_url = elgg_view('output/url', array(
				'text' => $poster->name,
				'href' => $poster->getURL(),
			));
			$post_url = elgg_view('output/url', array(
				'text' => elgg_echo('wall:tagged:post', array(), $language),
				'href' => $entity->getURL(),
			));

			$summary = elgg_echo('wall:tagged:notification:subject', array($poster_url, $post_url), $language);
			$subject = strip_tags($subject);
			$body = elgg_echo('wall:tagged:notification:message', array(
				$poster_url,
				$message,
				$post_url
			), $language);

			notify_user($to_guid, $from_guid, $subject, $body, array(
				'summary' => $summary,
				'object' => $entity,
				'action' => 'tagged',
			));
		}

		return true;
	}
}
