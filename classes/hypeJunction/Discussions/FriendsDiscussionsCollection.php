<?php

namespace hypeJunction\Discussions;

class FriendsDiscussionsCollection extends DefaultDiscussionsCollection {

	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return 'collection:object:discussion:friends';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCollectionType() {
		return 'friends';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getQueryOptions(array $options = []) {
		$options['relationship_guid'] = (int) $this->target->guid;
		$options['relationship'] = 'friend';
		$options['relationship_join_on'] = 'owner_guid';

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