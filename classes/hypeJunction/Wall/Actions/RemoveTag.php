<?php

namespace hypeJunction\Wall\Post;

/**
 * @property int         $guid
 * @property \ElggEntity $entity
 * @property \ElggUser   $user
 */
class RemoveTag extends \hypeJunction\Controllers\Action {

	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		parent::setup();
		$this->entity = get_entity($this->guid);
		$this->user = elgg_get_logged_in_user_entity();
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		if (!$this->entity) {
			throw new \hypeJunction\Exceptions\InvalidEntityException("Entity with guid $this->guid can not be loaded");
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {

		$relationship = check_entity_relationship($this->user->guid, 'tagged_in', $this->entity->guid);
		if ($relationship instanceof \ElggRelationship) {
			if ($relationship->delete()) {
				elgg_delete_river(array(
					'subject_guids' => $this->user->guid,
					'object_guids' => $this->entity->guid,
					'action_types' => 'tagged',
				));

				/**
				 * @todo: remove from access collection?
				 */
				$this->result->addMessage(elgg_echo('wall:remove_tag:success'));
				return;
			}
		}

		$this->result->addError(elgg_echo('wall:remove_tag:error'));
	}

}
