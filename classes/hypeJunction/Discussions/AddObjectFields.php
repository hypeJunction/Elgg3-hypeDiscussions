<?php

namespace hypeJunction\Discussions;

use Elgg\Event;
use hypeJunction\Fields\MetaField;

/**
 * AddObjectFields class.
 */
class AddObjectFields {

	/**
	 * Setup for fields
	 *
	 * @param Event $event Hook
	 * @return void
	 */
	public function __invoke(Event $event) {

		$fields = $event->getValue();
		/* @var $fields \hypeJunction\Fields\Collection */

		$entity = $event->getEntityParam();

		if ($entity instanceof \ElggDiscussion) {
			return;
		}

		if (!elgg_get_plugin_setting('post_discussions', 'hypediscussions')) {
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
