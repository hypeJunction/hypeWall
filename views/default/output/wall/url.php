<?php

namespace hypeJunction\Wall;

use hypeJunction\Util\Embedder;

$value = elgg_extract('value', $vars);

echo Embedder::getEmbedView($value, $vars);