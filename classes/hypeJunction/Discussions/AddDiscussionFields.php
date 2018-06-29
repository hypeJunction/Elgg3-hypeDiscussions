<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;
use hypeJunction\Fields\MetaField;

class AddDiscussionFields {

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

		$fields->remove('disable_comments');

		$fields->add('status', new MetaField([
			'#label' => false,
			'type' => 'checkbox',
			'default' => 'open',
			'value' => 'closed',
			'checked' => $entity->status === 'closed',
			'label' => elgg_echo('field:object:discussion:status'),
			'is_profile_field' => false,
			'switch' => true,
			'section' => 'sidebar',
			'priority' => 50,
		]));

		if (elgg_get_plugin_setting('max_comment_depth', 'hypeInteractions') > 1) {
			$fields->add('threads', new MetaField([
				'#label' => false,
				'type' => 'checkbox',
				'default' => 0,
				'value' => 1,
				'checked' => (bool) $entity->threads,
				'label' => elgg_echo('field:object:discussion:threads'),
				'is_profile_field' => false,
				'switch' => true,
				'section' => 'sidebar',
				'priority' => 51,
			]));
		}

		if (elgg_get_plugin_setting('post_discussions', 'hypeDiscussions') && !$entity->guid) {
			$fields->add('discussed_post_guid', new MetaField([
				'type' => 'guids',
				'options' => [
					'type' => 'object',
					'subtype' => '',
					'exclude_subtypes' => ['discussion'],
				],
				'value' => get_input('discussed_post_guid'),
				'limit' => 1,
				'multiple' => false,
				'is_profile_field' => false,
				'priority' => 10,
			]));
		}
	}
}