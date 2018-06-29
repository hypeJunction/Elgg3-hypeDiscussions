<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;

class EntityMenu {

	/**
	 * Setup entity menu
	 *
	 * @param Hook $hook Hook
	 * @return void
	 */
	public function __invoke(Hook $hook) {
		$entity = $hook->getEntityParam();
		$menu = $hook->getValue();
		/* @var $menu \Elgg\Menu\MenuItems */

		if (!$entity instanceof \ElggObject) {
			return;
		}

		if (!elgg_get_plugin_setting('post_discussions', 'hypeDiscussions')) {
			return;
		}

		if (!$entity->enable_discussions) {
			return;
		}

		$container = $entity->getContainerEntity();
		if (!$container instanceof \ElggGroup) {
			$container = elgg_get_logged_in_user_entity();
		}

		if ($container && $container->canWriteToContainer(0, 'object', 'discussion')) {
			$menu->add(\ElggMenuItem::factory([
				'name' => 'discuss',
				'icon' => 'question',
				'text' => elgg_echo('discussion:discuss'),
				'href' => elgg_generate_url('add:object:discussion', [
					'guid' => $container->guid,
					'discussed_post_guid' => $entity->guid,
				]),
				'priority' => 100,
			]));
		}
	}
}