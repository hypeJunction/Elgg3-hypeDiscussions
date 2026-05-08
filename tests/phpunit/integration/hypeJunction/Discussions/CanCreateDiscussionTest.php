<?php

namespace hypeJunction\Discussions;

use Elgg\Event;
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

        $hook = new Event(elgg(), 'container_permissions_check', 'object', true, [
            'user' => $user,
            'container' => $group,
            'subtype' => 'blog',
        ]);

        $handler = new CanCreateDiscussion();
        $this->assertNull($handler($hook));
    }

    public function testReturnsFalseWhenGroupForumDisabled(): void {
        $user = $this->createUser();
        $group = $this->createGroup();
        $group->forum_enable = 'no';
        $group->save();

        $hook = new Event(elgg(), 'container_permissions_check', 'object', true, [
            'user' => $user,
            'container' => $group,
            'subtype' => 'discussion',
        ]);

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

        $hook = new Event(elgg(), 'container_permissions_check', 'object', true, [
            'user' => $other,
            'container' => $group,
            'subtype' => 'discussion',
        ]);

        $handler = new CanCreateDiscussion();
        $this->assertFalse($handler($hook));
    }

    public function testNonGroupContainerRequiresSiteWideSetting(): void {
        $user = $this->createUser();
        $site = elgg_get_site_entity();

        // Ensure setting off
        $plugin = elgg_get_plugin_from_id('hypediscussions');
        if ($plugin) {
            $plugin->setSetting('site_wide_discussions', '');
        }

        $hook = new Event(elgg(), 'container_permissions_check', 'object', true, [
            'user' => $user,
            'container' => $site,
            'subtype' => 'discussion',
        ]);

        $handler = new CanCreateDiscussion();
        $this->assertFalse($handler($hook));
    }
}
