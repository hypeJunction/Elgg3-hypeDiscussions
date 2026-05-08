<?php

namespace hypeJunction\Discussions;

use Elgg\Event;
use Elgg\Menu\MenuItems;
use ElggMenuItem;
use ElggUser;

/**
 * OwnerBlockMenu class.
 */
class OwnerBlockMenu {

	/**
	 * Setup owner block menu
	 *
	 * @param Event $event Hook
	 * @return void
	 */
	public function __invoke(Event $event) {

		$entity = $event->getEntityParam();
		$menu = $event->getValue();
		/* @var $menu MenuItems */

		if (!$entity instanceof ElggUser) {
			return;
		}

		if (!elgg_get_plugin_setting('site_wide_discussions', 'hypediscussions')) {
			return;
		}

		$menu->add(ElggMenuItem::factory([
			'name' => 'discussions',
			'href' => elgg_generate_url('collection:object:discussion:owner', [
				'username' => $entity->username,
			]),
			'text' => elgg_echo('discussions'),
		]));
	}
}
