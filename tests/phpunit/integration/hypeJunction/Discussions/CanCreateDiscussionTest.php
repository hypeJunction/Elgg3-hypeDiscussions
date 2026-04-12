<?php

namespace hypeJunction\Discussions;

use Elgg\Hook;
use Elgg\IntegrationTestCase;

/**
 * Lock in behavior of container_permissions_check hook that
 * controls who can create a new discussion in a group.
 */
class CanCreateDiscussionTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    public function testReturnsNullForOtherSubtypes(): void {
        $user = $this->createUser();
        $group = $this->createGroup();

        $hook = new Hook($user, 'container_permissions_check', 'object', true);
        $hook->setParam('container', $group);
        $hook->setParam('subtype', 'blog');

        $handler = new CanCreateDiscussion();
        $this->assertNull($handler($hook));
    }

    public function testReturnsFalseWhenGroupForumDisabled(): void {
        $user = $this->createUser();
        $group = $this->createGroup();
        $group->forum_enable = 'no';
        $group->save();

        $hook = new Hook($user, 'container_permissions_check', 'object', true);
        $hook->setParam('container', $group);
        $hook->setParam('subtype', 'discussion');

        $handler = new CanCreateDiscussion();
        $this->assertFalse($handler($hook));
    }

    public function testNonAdminCannotCreateWhenAdminOnlyDiscussions(): void {
        $owner = $this->createUser();
        $other = $this->createUser();

        $group = $this->createGroup(['owner_guid' => $owner->guid]);
        $group->forum_enable = 'yes';
        $group->admin_only_discussions_enable = 'yes';
        $group->save();

        $hook = new Hook($other, 'container_permissions_check', 'object', true);
        $hook->setParam('container', $group);
        $hook->setParam('subtype', 'discussion');

        $handler = new CanCreateDiscussion();
        $this->assertFalse($handler($hook));
    }

    public function testNonGroupContainerRequiresSiteWideSetting(): void {
        $user = $this->createUser();
        $site = elgg_get_site_entity();

        // Ensure setting off
        elgg_set_plugin_setting('site_wide_discussions', '', 'hypeDiscussions');

        $hook = new Hook($user, 'container_permissions_check', 'object', true);
        $hook->setParam('container', $site);
        $hook->setParam('subtype', 'discussion');

        $handler = new CanCreateDiscussion();
        $this->assertFalse($handler($hook));
    }
}
