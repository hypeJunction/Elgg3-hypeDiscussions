<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;
use hypeJunction\Fields\MetaField;

class AddObjectFields {

	/**
	 * Setup for fields
	 *
	 * @param Hook $hook Hook
	 * @return void
	 */
	public function __invoke(Hook $hook) {

		$fields = $hook->getValue();
		/* @var $fields \hypeJunction\Fields\Collection */

		$entity = $hook->getEntityParam();

		if ($entity instanceof \ElggDiscussion) {
			return;
		}

		if (!elgg_get_plugin_setting('post_discussions', 'hypeDiscussions')) {
			return;
		}

		$fields->add('enable_discussions', new MetaField([
			'type' => 'checkbox',
			'switch' => true,
			'default' => 0,
			'value' => 1,
			'checked' => (bool) $entity->enable_discussions,
			'is_profile_field' => false,
			'priority' => 801,
			'section' => 'sidebar',
		]));

	}
}