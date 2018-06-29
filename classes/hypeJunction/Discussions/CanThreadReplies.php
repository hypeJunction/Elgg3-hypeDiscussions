<?php
/**
 *
 */

namespace hypeJunction\Discussions;

use Elgg\Hook;
use ElggDiscussion;
use hypeJunction\Discussion;

class CanThreadReplies {

	/**
	 * Enable discussion threads
	 *
	 * @elgg_plugin_hook permissions_check:comment object
	 *
	 * @param Hook $hook Hook
	 *
	 * @return bool
	 */
	public function __invoke(Hook $hook) {

		$entity = $hook->getEntityParam();
		$user = $hook->getUserParam();

		while ($entity instanceof \ElggComment) {
			$entity = $entity->getContainerEntity();
		}

		if ($entity instanceof ElggDiscussion) {
			$threads = $entity->threads;

			if (!$threads) {
				// Threading is disabled for this discussion
				return false;
			}

			return $entity->canComment($user->guid);
		}
	}
}