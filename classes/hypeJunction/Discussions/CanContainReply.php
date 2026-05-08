<?php

namespace hypeJunction\Discussions;

use Elgg\Event;

/**
 * CanContainReply class.
 */
class CanContainReply {

	/**
	 * Disable replies in closed discussions
	 *
	 * @elgg_event_handler container_logic_check object
	 *
	 * @param Event $event Hook
	 * @return bool|null
	 */
	public function __invoke(Event $event) {

		$user = $event->getUserParam();
		$container = $event->getParam('container');
		$subtype = $event->getParam('subtype');

		if ($subtype !== 'comment' || !$container instanceof \ElggDiscussion) {
			return null;
		}

		if ($container->status === 'closed') {
			return false;
		}
	}
}
