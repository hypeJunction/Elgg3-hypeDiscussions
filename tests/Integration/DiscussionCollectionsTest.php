<?php

namespace hypeJunction\Discussions\Tests\Integration;

use Elgg\IntegrationTestCase;
use hypeJunction\Discussions\DefaultDiscussionsCollection;
use hypeJunction\Discussions\FriendsDiscussionsCollection;
use hypeJunction\Discussions\GroupDiscussionsCollection;
use hypeJunction\Discussions\OwnedDiscussionsCollection;

/**
 * @group integration
 */
class DiscussionCollectionsTest extends IntegrationTestCase {

    public function testDefaultCollectionId() {
        $collection = new DefaultDiscussionsCollection();
        $this->assertEquals('collection:object:discussion:all', $collection->getId());
    }

    public function testDefaultCollectionType() {
        $collection = new DefaultDiscussionsCollection();
        $this->assertEquals('object', $collection->getType());
        $this->assertEquals('discussion', $collection->getSubtypes());
    }

    public function testDefaultCollectionCollectionType() {
        $collection = new DefaultDiscussionsCollection();
        $this->assertEquals('all', $collection->getCollectionType());
    }

    public function testFriendsCollectionId() {
        $collection = new FriendsDiscussionsCollection();
        $this->assertEquals('collection:object:discussion:friends', $collection->getId());
    }

    public function testGroupCollectionId() {
        $collection = new GroupDiscussionsCollection();
        $this->assertEquals('collection:object:discussion:group', $collection->getId());
    }

    public function testOwnedCollectionId() {
        $collection = new OwnedDiscussionsCollection();
        $this->assertEquals('collection:object:discussion:owner', $collection->getId());
    }

    public function testDefaultCollectionQueryOptions() {
        $collection = new DefaultDiscussionsCollection();
        $options = $collection->getQueryOptions();
        $this->assertTrue($options['preload_owners']);
        $this->assertTrue($options['distinct']);
    }

    public function testDefaultCollectionFilterOptionsRequiresLogin() {
        $collection = new DefaultDiscussionsCollection();
        // Without a logged-in user, filter options should be empty
        $this->assertEmpty($collection->getFilterOptions());
    }
}
