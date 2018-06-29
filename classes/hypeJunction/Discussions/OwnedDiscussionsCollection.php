<?php

namespace hypeJunction\Discussions;

class OwnedDiscussionsCollection extends DefaultDiscussionsCollection {

	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return 'collection:object:discussion:owner';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCollectionType() {
		return 'owner';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getQueryOptions(array $options = []) {
		$options['owner_guids'] = (int) $this->target->guid;

		return parent::getQueryOptions($options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getURL() {
		return elgg_generate_url($this->getId(), [
			'username' => $this->target->username,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilterOptions() {
		return [];
	}
}