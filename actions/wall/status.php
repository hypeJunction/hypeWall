<?php

$user = elgg_get_logged_in_user_entity();
$status = get_input('status');
$location = get_input('location');
$tags = get_input('wall_tag_guids');
$attachment = get_input('attachment', null, false);

$wall = new ElggObject();
$wall->subtype = 'hjwall';
$wall->access_id = get_input('access_id');
$wall->owner_guid = $user->guid;
$wall->container_guid = $user->guid;
$wall->title = '';
$wall->description = $status;

if ($wall->save()) {

	add_entity_relationship($user->guid, 'wall_owner', $wall->guid);

	add_to_river('river/object/hjwall/create', 'create', $user->guid, $wall->guid);

	if ($tags) {
		foreach ($tags as $tag) {
			$tagged_user = get_entity($tag);
			add_entity_relationship($tagged_user->guid, 'tagged_in', $wall->guid);

			$to = $tagged_user->guid;
			$from = $user->guid;
			$subject = elgg_echo('hj:wall:tagged:notification:subject', array($user->name));
			$message = elgg_echo('hj:wall:tagged:notification:message', array(
				$user->name,
				elgg_view_entity($wall, array('full_view' => false)),
				$wall->getURL()
			));

			notify_user($to, $from, $subject, $message);

			add_to_river('river/relationship/tagged', 'tagged', $tagged_user->guid, $wall->guid);
		}
	}

	$wall->location = $location;
	$wall->attachment = $attachment;

	$user->status = $wall->description . hj_wall_get_tags_str($wall);
	
	system_message(elgg_echo('hj:wall:create:success'));
	forward($wall->getURL());
} else {
	register_error(elgg_echo('hj:wall:create:error'));
	forward(REFERER);
}

