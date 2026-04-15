<?php

namespace hypeJunction\Discussions;

use Elgg\HooksRegistrationService\Hook;
use Elgg\IntegrationTestCase;
use hypeJunction\Discussion;

/**
 * Lock in behavior of the container_logic_check hook that blocks
 * new comments on closed discussions.
 */
class CanContainReplyTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
        elgg_get_session()->removeLoggedInUser();
    }

    protected function makeDiscussion(string $status): Discussion {
        $user = $this->createUser();
        elgg_get_session()->setLoggedInUser($user);
        $group = $this->createGroup();

        $d = new Discussion();
        $d->owner_guid = $user->guid;
        $d->container_guid = $group->guid;
        $d->access_id = ACCESS_PUBLIC;
        $d->title = 'T';
        $d->description = 'D';
        $d->status = $status;
        $d->save();

        return $d;
    }

    public function testReturnsNullForNonCommentSubtype(): void {
        $user = $this->createUser();
        $d = $this->makeDiscussion('open');

        $hook = new Hook(elgg(), 'container_logic_check', 'object', true, [
            'user' => $user,
            'container' => $d,
            'subtype' => 'blog',
        ]);

        $handler = new CanContainReply();
        $this->assertNull($handler($hook));

        $d->delete();
    }

    public function testReturnsNullForNonDiscussionContainer(): void {
        $user = $this->createUser();
        $container = $this->createObject(['subtype' => 'blog']);

        $hook = new Hook(elgg(), 'container_logic_check', 'object', true, [
            'user' => $user,
            'container' => $container,
            'subtype' => 'comment',
        ]);

        $handler = new CanContainReply();
        $this->assertNull($handler($hook));
    }

    public function testReturnsFalseWhenDiscussionClosed(): void {
        $user = $this->createUser();
        $d = $this->makeDiscussion('closed');

        $hook = new Hook(elgg(), 'container_logic_check', 'object', true, [
            'user' => $user,
            'container' => $d,
            'subtype' => 'comment',
        ]);

        $handler = new CanContainReply();
        $this->assertFalse($handler($hook));

        $d->delete();
    }

    public function testReturnsNullWhenDiscussionOpen(): void {
        $user = $this->createUser();
        $d = $this->makeDiscussion('open');

        $hook = new Hook(elgg(), 'container_logic_check', 'object', true, [
            'user' => $user,
            'container' => $d,
            'subtype' => 'comment',
        ]);

        $handler = new CanContainReply();
        $this->assertNull($handler($hook));

        $d->delete();
    }
}
