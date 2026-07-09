<?php
/**
 *
 */

namespace hypeJunction\Discussions;

use Elgg\Event;
use ElggDiscussion;
use hypeJunction\Discussion;

/**
 * CanThreadReplies class.
 */
class CanThreadReplies {

	/**
	 * Enable discussion threads
	 *
	 * @elgg_event_handler permissions_check:comment object
	 *
	 * @param Event $event Hook
	 *
	 * @return bool|null
	 */
	public function __invoke(Event $event) {

		$entity = $event->getEntityParam();
		$user = $event->getUserParam();

		while ($entity instanceof \ElggComment) {
			$entity = $entity->getContainerEntity();
		}

		if ($entity instanceof ElggDiscussion) {
			$threads = $entity->threads;

			if (!$threads) {
				// Threading is disabled for this discussion
				return false;
			}

			if (!$user instanceof \ElggUser) {
				// Anonymous: leave the permission at whatever core decided.
				return null;
			}

			return $entity->canComment($user->guid);
		}
	}
}
