<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;
use ElggDiscussion;
use hypeJunction\Discussion;

class CanCreateReply {

	/**
	 * Discussion replies should not inherit permissions from discussion but from the parent (group)
	 *
	 * @elgg_plugin_hook permissions_check:comment object
	 *
	 * @param Hook $hook Hoook
	 * @return bool|null
	 */
	public function __invoke(Hook $hook) {

		$user = $hook->getUserParam();
		$entity = $hook->getEntityParam();

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