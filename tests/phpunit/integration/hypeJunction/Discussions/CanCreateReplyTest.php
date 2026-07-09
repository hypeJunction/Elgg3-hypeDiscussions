<?php

namespace hypeJunction\Discussions;

use Elgg\Event;
use Elgg\IntegrationTestCase;
use hypeJunction\Discussion;

/**
 * Lock in behavior of the CanCreateReply permissions_check:comment handler,
 * which routes reply permissions to the parent group (not the discussion) and
 * blocks replies when the group forum tool is off.
 */
class CanCreateReplyTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
        _elgg_services()->session_manager->removeLoggedInUser();
    }

    /**
     * @return void
     */
    public function testReturnsNullForNonDiscussionEntity(): void {
        $user = $this->createUser();
        $entity = $this->createObject(['subtype' => 'blog']);

        $event = new Event(elgg(), 'permissions_check:comment', 'object', true, [
            'user' => $user,
            'entity' => $entity,
        ]);

        $handler = new CanCreateReply();
        $this->assertNull($handler($event));
    }

    /**
     * @return void
     */
    public function testReturnsFalseWhenGroupForumDisabled(): void {
        $user = $this->createUser();
        _elgg_services()->session_manager->setLoggedInUser($user);

        $group = $this->createGroup();
        $group->disableTool('forum');
        $group->save();

        $discussion = new Discussion();
        $discussion->owner_guid = $user->guid;
        $discussion->container_guid = $group->guid;
        $discussion->access_id = ACCESS_PUBLIC;
        $discussion->title = 'T';
        $discussion->description = 'D';
        $discussion->status = 'open';
        $this->assertNotFalse($discussion->save());

        $event = new Event(elgg(), 'permissions_check:comment', 'object', true, [
            'user' => $user,
            'entity' => $discussion,
        ]);

        $handler = new CanCreateReply();
        $this->assertFalse($handler($event));

        $discussion->delete();
        $group->delete();
    }
}
