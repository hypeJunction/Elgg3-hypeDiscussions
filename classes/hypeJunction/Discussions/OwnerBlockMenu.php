<?php

namespace hypeJunction\Discussions;


use Elgg\Hook;
use Elgg\Menu\MenuItems;
use ElggMenuItem;
use ElggUser;

class OwnerBlockMenu {

	/**
	 * Setup owner block menu
	 *
	 * @param Hook $hook Hook
	 * @return void
	 */
	public function __invoke(Hook $hook) {

		$entity = $hook->getEntityParam();
		$menu = $hook->getValue();
		/* @var $menu MenuItems */

		if (!$entity instanceof ElggUser) {
			return;
		}

		if (!elgg_get_plugin_setting('site_wide_discussions', 'hypeDiscussions')) {
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