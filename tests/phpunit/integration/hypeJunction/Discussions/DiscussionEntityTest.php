<?php

namespace hypeJunction\Discussions;

use Elgg\IntegrationTestCase;
use ElggDiscussion;
use hypeJunction\Discussion;

/**
 * Lock in entity behavior for hypeDiscussions.
 */
class DiscussionEntityTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
        _elgg_services()->session_manager->removeLoggedInUser();
    }

    /**
     * @return void
     */
    public function testDiscussionSubtypeMapsToCustomClass(): void {
        $user = $this->createUser();
        _elgg_services()->session_manager->setLoggedInUser($user);

        $group = $this->createGroup();

        $discussion = new Discussion();
        $discussion->owner_guid = $user->guid;
        $discussion->container_guid = $group->guid;
        $discussion->access_id = ACCESS_PUBLIC;
        $discussion->title = 'Test Topic';
        $discussion->description = 'Body';
        $this->assertNotFalse($discussion->save());

        _elgg_services()->entityCache->delete($discussion->guid);
        $loaded = get_entity($discussion->guid);

        $this->assertInstanceOf(ElggDiscussion::class, $loaded);
        $this->assertInstanceOf(Discussion::class, $loaded);
        $this->assertEquals('discussion', $loaded->getSubtype());
        $this->assertEquals('object', $loaded->getType());

        $discussion->delete();
    }

    /**
     * @return void
     */
    public function testDiscussionMetadataPersists(): void {
        $user = $this->createUser();
        _elgg_services()->session_manager->setLoggedInUser($user);

        $group = $this->createGroup();

        $discussion = new Discussion();
        $discussion->owner_guid = $user->guid;
        $discussion->container_guid = $group->guid;
        $discussion->access_id = ACCESS_PUBLIC;
        $discussion->title = 'Metadata Topic';
        $discussion->description = 'Body';
        $discussion->status = 'open';
        $discussion->threads = 1;
        $this->assertNotFalse($discussion->save());

        _elgg_services()->entityCache->delete($discussion->guid);
        $loaded = get_entity($discussion->guid);

        $this->assertEquals('open', $loaded->status);
        $this->assertEquals(1, (int) $loaded->threads);

        $discussion->delete();
    }

    /**
     * @return void
     */
    public function testSubtypeConstantMatchesElggDiscussion(): void {
        $this->assertEquals('discussion', Discussion::SUBTYPE);
    }
}
