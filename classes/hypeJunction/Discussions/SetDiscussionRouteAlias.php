<?php

namespace hypeJunction\Discussions;

use Elgg\Event;

/**
 * SetDiscussionRouteAlias class.
 */
class SetDiscussionRouteAlias {

	/**
	 * Alias 'discussions' with 'discussion' route
	 *
	 * @param Event $event Hook
	 *
	 * @return array
	 */
	public function __invoke(Event $event) {

		$return = $event->getValue();

		$identifier = elgg_extract('identifier', $return);
		$segments = elgg_extract('segments', $return);

		if ($identifier == 'discussions') {
			$return['identifier'] = 'discussion';
		}

		if (empty($segments)) {
			$return['segments'] = ['all'];
		}

		return $return;
	}
}
