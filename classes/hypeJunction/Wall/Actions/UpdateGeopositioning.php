<?php

namespace hypeJunction\Wall\Actions;

/**
 * @property string $location
 * @property float  $latitude
 * @property float  $longitude
 */
class UpdateGeopositioning extends \hypeJunction\Controllers\Action {

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		hypeWall()->geo->set($this->location, $this->latitude, $this->longitude);
	}

}
