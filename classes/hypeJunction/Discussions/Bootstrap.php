<?php

namespace hypeJunction\Discussions;

use Elgg\Includer;
use Elgg\PluginBootstrap;
use Elgg\Values;
use hypeJunction\Stash\Stash;

class Bootstrap extends PluginBootstrap {

	/**
	 * Get plugin root
	 * @return string
	 */
	protected function getRoot() {
		return $this->plugin->getPath();
	}

	/**
	 * {@inheritdoc}
	 */
	public function load() {
		Includer::requireFileOnce($this->getRoot() . '/autoloader.php');
		Includer::requireFileOnce($this->getRoot() . '/lib/functions.php');
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {
		elgg_register_plugin_hook_handler('route:rewrite', 'discussions', SetDiscussionRouteAlias::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {

		elgg_register_collection('collection:object:discussion:all', DefaultDiscussionsCollection::class);
		elgg_register_collection('collection:object:discussion:owner', OwnedDiscussionsCollection::class);
		elgg_register_collection('collection:object:discussion:friends', FriendsDiscussionsCollection::class);
		elgg_register_collection('collection:object:discussion:group', GroupDiscussionsCollection::class);
		elgg_register_collection('collection:object:discussion:post', PostDiscussionsCollection::class);

		elgg_register_plugin_hook_handler('uses:comments', 'object:discussion', [Values::class, 'getTrue']);
		elgg_register_plugin_hook_handler('uses:cover', 'object:discussion', [Values::class, 'getTrue']);
		elgg_register_plugin_hook_handler('uses:river', 'object:discussion', [Values::class, 'getTrue']);
		elgg_register_plugin_hook_handler('allow_attachments', 'object:blog', [Values::class, 'getTrue']);

		elgg_register_plugin_hook_handler('fields', 'object:discussion', AddDiscussionFields::class);
		elgg_register_plugin_hook_handler('fields', 'object', AddObjectFields::class);

		// Setup menus
		elgg_register_plugin_hook_handler('register', 'menu:site', SiteMenu::class);
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', OwnerBlockMenu::class);
		elgg_register_plugin_hook_handler('register', 'menu:entity', EntityMenu::class);

		// Configure permissions
		elgg_register_plugin_hook_handler('permissions_check:comment', 'object', CanThreadReplies::class);
		elgg_register_plugin_hook_handler('permissions_check:comment', 'object', CanCreateReply::class);

		elgg_register_plugin_hook_handler('container_logic_check', 'object', CanContainReply::class);
		elgg_register_plugin_hook_handler('container_permissions_check', 'object', CanCreateDiscussion::class);

		// Allow admin only discussion creation in groups
		elgg()->group_tools->register('admin_only_discussions', [
			'label' => elgg_echo('group:discussion:admin_only'),
			'default_on' => false,
		]);

		elgg_extend_view('page/elements/comments', 'discussions/profile/module/discussions');
		elgg_extend_view('page/components/interactions', 'discussions/profile/module/discussions');

		Stash::instance()->register(new RelatedDiscussionsCounter());

		elgg_unregister_notification_event('object', 'discussion');
		elgg_register_notification_event('object', 'discussion', ['publish']);
		elgg_register_plugin_hook_handler('prepare', 'notification:publish:object:discussion', 'discussion_prepare_notification');
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {
		// Cleanup discussions and group_tools registrations
		elgg_unregister_plugin_hook_handler('register', 'menu:filter:groups/all', 'discussion_setup_groups_filter_tabs');
		elgg_unregister_widget_type('group_forum_topics');

		elgg_unregister_plugin_hook_handler('prepare', 'notification:create:object:discussion', 'discussion_prepare_notification');
	}

	/**
	 * {@inheritdoc}
	 */
	public function shutdown() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function activate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function deactivate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function upgrade() {

	}

}