<?php

namespace hypeJunction\Discussions;

use Elgg\Event;
use Elgg\IntegrationTestCase;
use hypeJunction\Discussion;

/**
 * Lock in behavior of threading permissions check.
 */
class CanThreadRepliesTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
        _elgg_services()->session_manager->removeLoggedInUser();
    }

    /**
     * @param int $threads
     * @return Discussion
     */
    protected function makeDiscussion(int $threads): Discussion {
        $user = $this->createUser();
        _elgg_services()->session_manager->setLoggedInUser($user);
        $group = $this->createGroup();

        $d = new Discussion();
        $d->owner_guid = $user->guid;
        $d->container_guid = $group->guid;
        $d->access_id = ACCESS_PUBLIC;
        $d->title = 'T';
        $d->description = 'D';
        $d->status = 'open';
        $d->threads = $threads;
        $d->save();

        return $d;
    }

    /**
     * @return void
     */
    public function testReturnsFalseWhenThreadingDisabled(): void {
        $user = $this->createUser();
        $d = $this->makeDiscussion(0);

        $hook = new Event(elgg(), 'permissions_check:comment', 'object', true, [
            'user' => $user,
            'entity' => $d,
        ]);

        $handler = new CanThreadReplies();
        $this->assertFalse($handler($hook));

        $d->delete();
    }

    /**
     * @return void
     */
    public function testReturnsNullForNonDiscussionEntity(): void {
        $user = $this->createUser();
        $entity = $this->createObject(['subtype' => 'blog']);

        $hook = new Event(elgg(), 'permissions_check:comment', 'object', true, [
            'user' => $user,
            'entity' => $entity,
        ]);

        $handler = new CanThreadReplies();
        $this->assertNull($handler($hook));
    }
}
