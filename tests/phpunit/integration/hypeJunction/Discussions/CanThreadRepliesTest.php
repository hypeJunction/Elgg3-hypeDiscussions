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

    /**
     * The handler is registered on permissions_check:comment, and
     * UserCapabilities::canComment() TRIGGERS that event. Calling canComment()
     * from inside the handler therefore recursed until the stack blew:
     * "Maximum call stack size ... reached. Infinite recursion?" — a hard 500 on
     * /discussion/all for every logged-in user. Go through the real API so a
     * reintroduced canComment() call blows up here rather than in production.
     *
     * @return void
     */
    public function testThreadedDiscussionPermissionCheckDoesNotRecurse(): void {
        $user = $this->createUser();
        _elgg_services()->session_manager->setLoggedInUser($user);

        $d = $this->makeDiscussion(1);

        // Fires permissions_check:comment, which invokes the registered handler.
        $this->assertIsBool($d->canComment($user->guid));

        $d->delete();
    }

    /**
     * Anonymous visitors have no user param. Dereferencing it fataled every
     * anonymous request to /discussion/all with a TypeError on canComment(null).
     *
     * @return void
     */
    public function testReturnsNullForAnonymousVisitor(): void {
        $d = $this->makeDiscussion(1);
        _elgg_services()->session_manager->removeLoggedInUser();

        $hook = new Event(elgg(), 'permissions_check:comment', 'object', true, [
            'user' => null,
            'entity' => $d,
        ]);

        $handler = new CanThreadReplies();
        $this->assertNull($handler($hook));

        $d->delete();
    }
}
