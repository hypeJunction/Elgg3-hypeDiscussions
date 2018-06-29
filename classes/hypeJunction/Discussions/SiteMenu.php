<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;

class SiteMenu {

	/**
	 * Register site menu item
	 *
	 * @param Hook $hook Hook
	 * @return void
	 */
	public function __invoke(Hook $hook) {
		$menu = $hook->getValue();

		$menu->add(\ElggMenuItem::factory([
			'name' => 'discussion',
			'href' => elgg_generate_url('collection:object:discussion:all'),
			'text' => elgg_echo('discussions'),
			'icon' => 'question',
		]));
	}
}