<?php

namespace hypeJunction\Wall;

/**
 * Config
 */
class Config extends \hypeJunction\Config {

	/**
	 * {@inheritdoc}
	 */
	public function getDefaults() {
		return array(
			'legacy_mode' => true,
			'pagehandler_id' => 'wall',
			'model' => 1,
			'geopositioning' => false,
			'tag_friends' => false,
			'status' => true,
			'url' => true,
			'photo' => true,
			'content' => true,
			'default_form' => 'status',
			'third_party_wall' => false,
			'status_input_type' => 'plaintext',
		);
	}

	public function setLegacyConfig() {
		define('WALL_MODEL', $this->get('model'));
		define('WALL_MODEL_WALL', 1);
		define('WALL_MODEL_WIRE', 2);

		define('WALL_SUBTYPE', (WALL_MODEL == WALL_MODEL_WIRE) ? 'thewire' : 'hjwall');

		define('WALL_GEOPOSITIONING', $this->get('geopositioning'));
		define('WALL_TAG_FRIENDS', $this->get('tag_friends'));
	}

	public function getPostSubtype() {
		$model = $this->get('model');
		return ($model === 2) ? 'thewire' : 'hjwall';
	}

}
