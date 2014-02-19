<?php

namespace hypeJunction\Wall;

/**
 * Listen to the 'publish','object' event and send out notifications
 * to interested users, as well as anyone tagged
 *
 * @param string $event			Equals 'publish'
 * @param string $entity_type	Equals 'object'
 * @param ElggEntity $entity	Published entity
 */
function send_notifications($event, $entity_type, $entity) {

	if ($event !== 'publish' || $entity_type !== 'object' || !elgg_instanceof($entity) || $entity->origin !== 'wall') {
		return true;
	}

	// We only want to notify about wire posts and wall posts, all content created therewith is implied
	$accepted_subtypes = array('hjwall', 'thewire');
	if (!in_array($entity->getSubtype(), $accepted_subtypes)) {
		return true;
	}

	$poster = $entity->getOwnerEntity();
	$container = $entity->getContainerEntity();
	$message = format_wall_message($entity, true);

	$sent = array(elgg_get_logged_in_user_guid(), $poster->guid, $container->guid);

	// Notify wall owner
	if ($poster->guid !== $container->guid && elgg_instanceof($container, 'user')) {
		$to = $container->guid;
		$from = $poster->guid;

		$target = elgg_echo("wall:target:{$entity->getSubtype()}");
		$ownership = elgg_echo('wall:ownership:your', array($target));

		$subject = elgg_echo('wall:new:notification:subject', array($poster->name, $ownership));
		$body = elgg_echo('wall:new:notification:message', array(
			$poster->name,
			$ownership,
			$message,
			$entity->getURL()
		));

		notify_user($to, $from, $subject, $body);
	}

	// Notify tagged users
	$tagged_friends = get_tagged_friends($entity);
	foreach ($tagged_friends as $tagged_friend) {
		// user tagged herself or the wall owner
		if ($tagged_friend->guid == $poster->guid || $tagged_friend->guid == $container->guid) {
			continue;
		}

		$sent[] = $tagged_friend->guid;

		$to = $tagged_friend->guid;
		$from = $poster->guid;
		$subject = elgg_echo('wall:tagged:notification:subject', array($poster->name));
		$body = elgg_echo('wall:tagged:notification:message', array(
			$poster->name,
			$message,
			$entity->getURL()
		));

		notify_user($to, $from, $subject, $body);
	}

	elgg_push_context('widgets');
	$default_msg_body = elgg_view_entity($entity, array('full_view' => false));
	elgg_pop_context();

	global $NOTIFICATION_HANDLERS;

	// Get users interested in content from this person and notify them
	// (Person defined by container_guid so we can also subscribe to groups if we want)
	foreach ($NOTIFICATION_HANDLERS as $method => $foo) {
		$interested_users = elgg_get_entities_from_relationship(array(
			'site_guids' => ELGG_ENTITIES_ANY_VALUE,
			'relationship' => 'notify' . $method,
			'relationship_guid' => $entity->container_guid,
			'inverse_relationship' => true,
			'type' => 'user',
			'limit' => false
		));

		if ($interested_users && is_array($interested_users)) {
			foreach ($interested_users as $user) {
				if ($user instanceof ElggUser && !$user->isBanned() && !in_array($user->guid, $sent)) {
					if (has_access_to_entity($entity, $user) && $entity->access_id != ACCESS_PRIVATE) {
						$body = elgg_trigger_plugin_hook('notify:entity:message', 'object', array(
							'entity' => $entity,
							'to_entity' => $user,
							'method' => $method), $default_msg_body);

						if ($body !== false) {
							notify_user($user->guid, $entity->container_guid, $subject, $body, null, array($method));
						}
					}
				}
			}
		}
	}

	return true;
}
