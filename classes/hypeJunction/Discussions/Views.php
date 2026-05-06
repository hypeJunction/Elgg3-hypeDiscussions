<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;
use ElggGroup;

/**
 * Views class.
 */
class Views {

	/**
	 * Resolve missing container_guid on the discussion form from the entity or page owner.
	 *
	 * @param Hook $hook "view_vars", "forms/discussion/save"
	 * @return array|null
	 */
	public static function filterDiscussionFormVars(Hook $hook) {

		$return = $hook->getValue();

		$guid = elgg_extract('guid', $return);
		$container_guid = elgg_extract('container_guid', $return);
		if ($container_guid) {
			return;
		}

		$entity = null;
		if ($guid) {
			$entity = get_entity($guid);
		}

		if ($entity) {
			$return['entity'] = $entity;
			$container_guid = $entity->getContainerGUID();
		} else {
			$page_owner = elgg_get_page_owner_entity();
			if ($page_owner instanceof ElggGroup) {
				$container_guid = $page_owner->guid;
			}
		}

		$return['container_guid'] = $container_guid;
		return $return;
	}

	/**
	 * Hide the group discussions widget when forums are disabled on the group.
	 *
	 * @param Hook $hook "view_vars", "page/layouts/widgets"
	 * @return void
	 */
	public static function filterWidgetLayoutVars(Hook $hook) {

		$owner = elgg_get_page_owner_entity();
		if ($owner instanceof ElggGroup && $owner->forum_enable != 'yes') {
			elgg_unregister_widget_type('group_discussions');
		}
	}
}
