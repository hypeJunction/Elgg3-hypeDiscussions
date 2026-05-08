<?php

namespace hypeJunction\Discussions;

use Elgg\Event;

/**
 * CanCreateDiscussion class.
 */
class CanCreateDiscussion {

	/**
	 * Check group settings to disallow creation of new discussions
	 *
	 * @elgg_event_handler container_permissions_check object
	 *
	 * @param Event $event Hook
	 * @return bool|null
	 */
	public function __invoke(Event $event) {

		$user = $event->getUserParam();
		$container = $event->getParam('container');
		$subtype = $event->getParam('subtype');

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
			if (!elgg_get_plugin_setting('site_wide_discussions', 'hypediscussions')) {
				return false;
			}
		}
	}
}
