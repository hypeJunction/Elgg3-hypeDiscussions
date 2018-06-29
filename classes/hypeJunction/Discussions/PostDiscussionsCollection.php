<?php

namespace hypeJunction\Discussions;

class PostDiscussionsCollection extends DefaultDiscussionsCollection {

	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return 'collection:object:discussion:post';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCollectionType() {
		return 'post';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getQueryOptions(array $options = []) {
		$options['metadata_name_value_pairs']['discussed_post_guid'] = (int) $this->target->guid;

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

	/**
	 * {@inheritdoc}
	 */
	public function getMenu() {
		$entity = $this->getTarget();

		if (!$entity instanceof \ElggObject) {
			return [];
		}

		if (!elgg_get_plugin_setting('post_discussions', 'hypeDiscussions')) {
			return [];
		}

		if (!$entity->enable_discussions) {
			return [];
		}

		$menu = [];

		$container = $entity->getContainerEntity();
		if (!$container instanceof \ElggGroup) {
			$container = elgg_get_logged_in_user_entity();
		}

		if ($container && $container->canWriteToContainer(0, 'object', 'discussion')) {
			$menu[] = \ElggMenuItem::factory([
				'name' => 'discuss',
				'icon' => 'question',
				'text' => elgg_echo('discussion:discuss'),
				'href' => elgg_generate_url('add:object:discussion', [
					'guid' => $container->guid,
					'discussed_post_guid' => $entity->guid,
				]),
				'priority' => 100,
			]);
		}

		return $menu;
	}
}