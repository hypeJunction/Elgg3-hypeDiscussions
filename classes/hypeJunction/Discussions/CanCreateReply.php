<?php

namespace hypeJunction\Discussions;

use Elgg\Event;
use ElggDiscussion;
use hypeJunction\Discussion;

/**
 * CanCreateReply class.
 */
class CanCreateReply {

	/**
	 * Discussion replies should not inherit permissions from discussion but from the parent (group)
	 *
	 * @elgg_event_handler permissions_check:comment object
	 *
	 * @param Event $event Hoook
	 * @return bool|null
	 */
	public function __invoke(Event $event) {

		$user = $event->getUserParam();
		$entity = $event->getEntityParam();

		if (!$entity instanceof ElggDiscussion) {
			return null;
		}

		if (!$entity->canWriteToContainer($user->guid, 'object', 'comment')) {
			return false;
		}

		$group = $entity->getContainerEntity();
		if ($group instanceof \ElggGroup) {
			if (!$group->isToolEnabled('forum')) {
				return false;
			}

			return $group->canWriteToContainer($user->guid);
		}
	}
}
