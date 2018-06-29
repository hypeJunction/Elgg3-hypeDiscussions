<?php

namespace hypeJunction\Discussions;

class GroupDiscussionsCollection extends DefaultDiscussionsCollection {

	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return 'collection:object:discussion:group';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCollectionType() {
		return 'group';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getQueryOptions(array $options = []) {
		$options['container_guids'] = (int) $this->target->guid;

		return parent::getQueryOptions($options);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getURL() {
		return elgg_generate_url($this->getId(), [
			'guid' => $this->target->guid,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilterOptions() {
		return [];
	}
}