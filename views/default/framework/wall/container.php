<?php

use hypeJunction\Wall\Post;

if (!elgg_is_logged_in()) {
	return;
}

if (!elgg_in_context('activity') && !elgg_in_context('wall')) {
	return;
}

$entity = elgg_extract('entity', $vars);

if ($entity) {
	$user = $entity->getOwnerEntity();
} else {
	$user = elgg_get_logged_in_user_entity();
	$container = elgg_get_page_owner_entity();
	if (!$container) {
		$container = $user;
	}
	if (!$container->canWriteToContainer($user->guid, 'object', Post::SUBTYPE)) {
		return;
	}
}

$forms = elgg_view_form('wall/status', array(
	'id' => 'wall-form-status',
	'class' => ['wall-form', $entity ? 'wall-form-edit' : ''],
		), $vars);

if (!$entity) {
	$forms .= elgg_view('framework/wall/container/extend', $vars);
}

$forms = elgg_format_element('div', [
	'class' => 'wall-forms',
		], $forms);

$tabs = elgg_view_menu('wall-filter', array(
	'sort_by' => 'priority'
		));

$icon = elgg_view_entity_icon($user, 'small', array(
	'use_hover' => false,
	'link_class' => 'wall-poster-icon-block',
	'img_class' => 'wall-poster-avatar'
		));

$class = (elgg_in_context('activity')) ? 'wall-river' : 'wall-to-wall';
echo elgg_view_image_block($icon, $tabs . $forms, [
	'class' => "wall-container clearfix $class",
]);
?>
<script>
	require(['framework/wall/container'], function (lib) {
		lib.init();
	});
</script>