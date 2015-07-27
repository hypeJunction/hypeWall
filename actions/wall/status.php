<?php

use hypeJunction\Wall\Actions\SavePost;

$result = hypeApps()->actions->execute(new SavePost());
forward($result->getForwardURL());