<?php

namespace hypeJunction\Discussions;

use Elgg\Event;

/**
 * SiteMenu class.
 */
class SiteMenu {

	/**
	 * Register site menu item
	 *
	 * @param Event $event Hook
	 * @return void
	 */
	public function __invoke(Event $event) {
		$menu = $event->getValue();

		$menu->add(\ElggMenuItem::factory([
			'name' => 'discussion',
			'href' => elgg_generate_url('collection:object:discussion:all'),
			'text' => elgg_echo('discussions'),
			'icon' => 'question',
		]));
	}
}
