<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;

class CanCreateDiscussion {

	/**
	 * Check group settings to disallow creation of new discussions
	 *
	 * @elgg_plugin_hook container_permissions_check object
	 *
	 * @param Hook $hook Hook
	 * @return bool|null
	 */
	public function __invoke(Hook $hook) {

		$user = $hook->getUserParam();
		$container = $hook->getParam('container');
		$subtype = $hook->getParam('subtype');

		if ($subtype !== 'discussion') {
			return null;
		}

		if ($container instanceof \ElggGroup) {
			if (!$container->isToolEnabled('forum')) {
				return false;
			}

			if ($container->isToolEnabled('admin_only_discussions') && !$container->canEdit($user->guid)) {
				// New discussions are restricted to group admins
				return false;
			}
		} else {
			if (!elgg_get_plugin_setting('site_wide_discussions', 'hypeDiscussions')) {
				return false;
			}
		}
	}
}