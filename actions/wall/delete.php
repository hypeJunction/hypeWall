<?php

use hypeJunction\Controllers\DeleteAction;

$result = hypeApps()->actions->execute(new DeleteAction());
forward($result->getForwardURL());
