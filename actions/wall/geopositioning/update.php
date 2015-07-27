<?php

use hypeJunction\Wall\Actions\UpdateGeopositioning;

$result = hypeApps()->actions->execute(new UpdateGeopositioning());
forward($result->getForwardURL());

