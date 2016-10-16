<?php

/**
 * Displays a list of wall posts
 *
 * @uses $vars['entity']     User or group entity
 * @uses $vars['post_guids'] GUIDs of wall posts to list
 * @uses $vars['options']    Additional list view options
 */
$entity = elgg_extract('entity', $vars);
$guid = (int) $entity->guid;

$post_guids = (array) elgg_extract('post_guids', $vars, array());
$post_guids = array_filter($post_guids);

if (empty($post_guids)) {
	$object_guids = ELGG_ENTITIES_ANY_VALUE;
	$no_results = elgg_echo('wall:empty');
} else {
	$object_guids = $post_guids;
	$no_results = elgg_echo('wall:notfound');
}

$base_url = elgg_http_add_url_query_elements(elgg_normalize_url("wall/$entity->guid"), array(
	'post_guids' => $post_guids,
		));

$list_class = (array) elgg_extract('list_class', $vars, array());
$list_class[] = 'wall-post-list';

$item_class = (array) elgg_extract('item_class', $vars, array());
$item_class[] = 'wall-post clearfix';

$dbprefix = elgg_get_config('dbprefix');

$options = (array) elgg_extract('options', $vars, array());
$list_options = array(
	'full_view' => true,
	'limit' => elgg_extract('limit', $vars, elgg_get_config('default_limit')) ? : 10,
	'list_class' => implode(' ', $list_class),
	'item_class' => implode(' ', $item_class),
	'no_results' => $no_results,
	'pagination' => elgg_is_active_plugin('hypeLists') || !elgg_in_context('widgets'),
	'pagination_type' => 'infinite',
	'base_url' => $base_url,
	'list_id' => "wall-$guid",
	//'auto_refresh' => 30,
);

$getter_options = array(
	'types' => 'object',
	'subtypes' => hypeJunction\Wall\Post::SUBTYPE,
	'object_guids' => $object_guids,
	'action_types' => array('create'),
	'wheres' => array(
		"({$guid} = rv.target_guid
				OR EXISTS (SELECT 1 FROM {$dbprefix}entity_relationships WHERE guid_one = {$guid} AND relationship = 'tagged_in' AND guid_two = rv.object_guid))",
	),
);

$options = array_merge($list_options, $options, $getter_options);

$content = elgg_list_river($options);

echo $content;
