<?php
/**
 * Add session geopositioning data to the config
 */
if (!hypeWall()->config->get('geopositioning')) {
	return;
}

$geopositioning = json_encode(hypeWall()->geo->get());
?>

//<script>
	elgg.session.geopositioning = <?php echo $geopositioning ?>;