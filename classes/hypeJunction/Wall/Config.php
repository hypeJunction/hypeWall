<?php

namespace hypeJunction\Wall;

/**
 * Config
 * @property int    $model
 * @property bool   $geopositioning
 * @property bool   $tag_friends
 * @property bool   $url
 * @property bool   $status
 * @property bool   $photo
 * @property bool   $content
 * @property string $default_form
 * @property bool   $third_party_wall
 * @property string $status_input_type
 * @property int    $character_limit
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
			'character_limit' => 0,
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
