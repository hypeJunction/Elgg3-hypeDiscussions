<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;

class CanContainReply {

	/**
	 * Disable replies in closed discussions
	 *
	 * @elgg_plugin_hook container_logic_check object
	 *
	 * @param Hook $hook Hook
	 * @return bool|null
	 */
	public function __invoke(Hook $hook) {

		$user = $hook->getUserParam();
		$container = $hook->getParam('container');
		$subtype = $hook->getParam('subtype');

		if ($subtype !== 'comment' || !$container instanceof \ElggDiscussion) {
			return null;
		}

		if ($container->status === 'closed') {
			return false;
		}
	}
}