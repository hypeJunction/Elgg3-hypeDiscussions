<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;

class SetDiscussionRouteAlias {

	/**
	 * Alias 'discussions' with 'discussion' route
	 *
	 * @param Hook $hook Hook
	 *
	 * @return array
	 */
	public function __invoke(Hook $hook) {

		$return = $hook->getValue();

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