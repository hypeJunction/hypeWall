<?php

namespace hypeJunction\Wall\Post;

/**
 * @property \hypeJunction\Wall\Post $entity
 * @property int                     $guid
 */
class deleteAction extends \hypeJunction\Controllers\Action {

	const CLASSNAME = __CLASS__;

	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		parent::setup();
		$this->entity = get_entity($this->guid);
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {

		if ($this->entity->delete()) {
			$this->result->addMessage(elgg_echo('wall:delete:success'));
			$this->result->setForwardURL($this->entity->getURL());
		} else {
			$this->result->addError(elgg_echo('wall:delete:error'));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		if (!$this->entity instanceof \hypeJunction\Wall\Post) {
			throw new \hypeJunction\Exceptions\InvalidEntityException("Entity with $this->guid not found");
		}
		return true;
	}

}
