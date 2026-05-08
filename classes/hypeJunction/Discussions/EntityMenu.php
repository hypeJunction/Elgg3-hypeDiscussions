<?php

namespace hypeJunction\Discussions;

use Elgg\Event;

/**
 * EntityMenu class.
 */
class EntityMenu {

	/**
	 * Setup entity menu
	 *
	 * @param Event $event Hook
	 * @return void
	 */
	public function __invoke(Event $event) {
		$entity = $event->getEntityParam();
		$menu = $event->getValue();
		/* @var $menu \Elgg\Menu\MenuItems */

		if (!$entity instanceof \ElggObject) {
			return;
		}

		if (!elgg_get_plugin_setting('post_discussions', 'hypediscussions')) {
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
