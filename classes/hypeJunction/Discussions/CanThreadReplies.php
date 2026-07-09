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

			// NOT canComment(): UserCapabilities::canComment() triggers this very
			// event, so calling it from a handler recurses until the stack blows
			// ("Maximum call stack size ... reached. Infinite recursion?").
			// canWriteToContainer() is precisely the default value core passes into
			// the event, so this returns the same answer without re-entering it.
			return $entity->canWriteToContainer($user->guid, 'object', 'comment');
		}
	}
}
