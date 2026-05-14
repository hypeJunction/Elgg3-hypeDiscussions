<?php

namespace hypeJunction\Discussions;

use Elgg\DefaultPluginBootstrap;
use hypeJunction\Stash\Stash;

class Bootstrap extends DefaultPluginBootstrap {

	public function load(): void {
		$pluginRoot = dirname(__DIR__, 3);
		$autoloader = $pluginRoot . '/autoloader.php';
		if (file_exists($autoloader)) {
			require_once $autoloader;
		}
		$functions = $pluginRoot . '/lib/functions.php';
		if (file_exists($functions)) {
			require_once $functions;
		}
	}

	public function init(): void {
		if (function_exists('elgg_register_collection')) {
			elgg_register_collection('collection:object:discussion:all', DefaultDiscussionsCollection::class);
			elgg_register_collection('collection:object:discussion:owner', OwnedDiscussionsCollection::class);
			elgg_register_collection('collection:object:discussion:friends', FriendsDiscussionsCollection::class);
			elgg_register_collection('collection:object:discussion:group', GroupDiscussionsCollection::class);
			elgg_register_collection('collection:object:discussion:post', PostDiscussionsCollection::class);
		}

		if (elgg()->has('group_tools')) {
			elgg()->group_tools->register('admin_only_discussions', [
				'label' => elgg_echo('group:discussion:admin_only'),
				'default_on' => false,
			]);
		}

		elgg_extend_view('page/elements/comments', 'discussions/profile/module/discussions');
		elgg_extend_view('page/components/interactions', 'discussions/profile/module/discussions');

		if (class_exists(Stash::class)) {
			Stash::instance()->register(new RelatedDiscussionsCounter());
		}

		elgg_unregister_notification_event('object', 'discussion');
		elgg_register_notification_event('object', 'discussion', 'publish');
	}

	public function ready(): void {
		elgg_unregister_event_handler('register', 'menu:filter:groups/all', 'discussion_setup_groups_filter_tabs');
		elgg_unregister_widget_type('group_forum_topics');
		elgg_unregister_event_handler('prepare', 'notification:create:object:discussion', 'discussion_prepare_notification');
	}
}
