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

		if (!$user instanceof \ElggUser) {
			// Anonymous visitors never get write permission.
			return false;
		}

		if (!$entity->canWriteToContainer($user->guid, 'object', 'comment')) {
			return false;
		}

		$group = $entity->getContainerEntity();
		if ($group instanceof \ElggGroup) {
			if (!$group->isToolEnabled('forum')) {
				return false;
			}

			// Elgg 7 requires BOTH $type and $subtype; a bare call throws
			// "canWriteToContainer requires $type and $subtype to be set".
			// A discussion reply is stored as an object/comment in the group.
			return $group->canWriteToContainer($user->guid, 'object', 'comment');
		}
	}
}
