<?php

namespace hypeJunction\Wall\Session;

/**
 * @property string $location
 * @property float  $latitude
 * @property float  $longitude
 */
class updateGeopAction extends \hypeJunction\Controllers\Action {

	const CLASSNAME = __CLASS__;

	/**
	 * {@inheritdoc}
	 */
	public function execute() {
		hypeWall()->geo->set($this->location, $this->latitude, $this->longitude);
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		return true;
	}

}
