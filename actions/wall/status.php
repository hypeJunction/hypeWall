<?php

use hypeJunction\Wall\Actions\SavePost;

$result = hypeApps()->actions->execute(new SavePost());

if (elgg_is_xhr()) {
	echo $result->output;
}

forward($result->getForwardURL());
