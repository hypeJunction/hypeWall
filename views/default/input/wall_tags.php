<?php
if (isset($vars['class'])) {
	$vars['class'] = "hj-wall-tags-autocomplete {$vars['class']}";
} else {
	$vars['class'] = "hj-wall-tags-autocomplete";
}

$user = elgg_get_logged_in_user_entity();
$entities = $user->getFriends("", 0);

if (is_array($entities)) {
	foreach ($entities as $entity) {
		$result = array(
			//'icon' => $entity->getIconURL('tiny'),
			'username' => $entity->username,
			'value' => $entity->name,
			'guid' => $entity->guid,
		);
		$results[] = $result;
	}
}

if ($results) {
	$results = json_encode($results);
	?>
	<script type="text/javascript">
	    elgg.provide('hj.wall.base');
	    hj.wall.base.sourceentities = <?php echo $results ?>;
	</script>
	<?php
	$guids = $vars['value'];
	$vars['value'] = '';
	$vars['placeholder'] = elgg_echo('hj:wall:tag:friends');
	$vars['data-options'] = json_encode(htmlentities($vars['options'], ENT_QUOTES, 'UTF-8'));
	unset($vars['options']);

	elgg_load_js('hj.wall.base');
	echo '<ul id="hj-wall-tags" class="elgg-list hj-wall-tags-list clearfix">';
	if (is_array($guids)) {
		foreach ($guids as $guid) {
			$guid = trim($guid);
			$tag = get_entity($guid);
			if (elgg_instanceof($tag)) {
				$icon = $tag->getIconURL('tiny');
				$remove = elgg_echo('remove');
				$html = '<li class="hj-wall-tag clearfix">
                            <span class="hj-left">' . $tag->name . '</span>
                            <a class="hj-wall-tag-remove hj-right" rel="tag' . $tag->guid . '" href="javascript:void(0)"></a>
                            <input type="hidden" name="wall_tag_guids[]" value="' . $tag->guid . '" />
                        </li>';
				echo $html;
			}
		}
	}
	echo '</ul>';
	?>

	<input type="text" <?php echo elgg_format_attributes($vars); ?> />
	<?php
	$guids = explode(',', $guids);

} else {
	return true;
}