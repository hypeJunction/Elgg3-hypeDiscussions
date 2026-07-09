<?php

namespace hypeJunction\Discussions;

use Elgg\IntegrationTestCase;
use hypeJunction\Discussion;
use hypeJunction\Lists\EntityList;

/**
 * Regression: a group's discussion listing (/discussion/group/{guid}) must
 * surface the group's threads and expose a /discussion/view/{guid} permalink
 * for each one, otherwise listing-to-thread navigation is broken.
 *
 * Reproduces the bodyology 7.x gap where group 130 held 8+ discussions but
 * the group listing exposed 0 thread permalinks.
 */
class GroupDiscussionsListingTest extends IntegrationTestCase {

    /**
     * @var \ElggUser
     */
    protected $user;

    public function up() {
        $this->user = $this->createUser();
        _elgg_services()->session_manager->setLoggedInUser($this->user);
    }

    public function down() {
        _elgg_services()->session_manager->removeLoggedInUser();
    }

    /**
     * @param \ElggGroup $group
     * @param string     $title
     * @return Discussion
     */
    protected function makeDiscussion(\ElggGroup $group, string $title): Discussion {
        $d = new Discussion();
        $d->owner_guid = $this->user->guid;
        $d->container_guid = $group->guid;
        $d->access_id = ACCESS_PUBLIC;
        $d->title = $title;
        $d->description = 'Body of ' . $title;
        $d->status = 'open';
        $this->assertNotFalse($d->save());

        return $d;
    }

    /**
     * The group collection's query must return exactly the discussions that
     * live in that group (container filter), not zero and not everything.
     *
     * @return void
     */
    public function testGroupListingReturnsThatGroupsDiscussions(): void {
        $group = $this->createGroup(['owner_guid' => $this->user->guid]);
        $other_group = $this->createGroup(['owner_guid' => $this->user->guid]);

        $a = $this->makeDiscussion($group, 'Alpha topic');
        $b = $this->makeDiscussion($group, 'Bravo topic');
        $c = $this->makeDiscussion($group, 'Charlie topic');

        // Lives in a different group — must NOT surface in this listing.
        $foreign = $this->makeDiscussion($other_group, 'Foreign topic');

        $collection = new GroupDiscussionsCollection($group);

        /* @var $list EntityList */
        $list = $collection->getList();
        $this->assertInstanceOf(EntityList::class, $list);

        $items = $list->get(50, 0);
        $guids = array_map(static function ($e) {
            return (int) $e->guid;
        }, $items);

        $this->assertContains((int) $a->guid, $guids);
        $this->assertContains((int) $b->guid, $guids);
        $this->assertContains((int) $c->guid, $guids);
        $this->assertNotContains((int) $foreign->guid, $guids, 'Group listing leaked a foreign group discussion');

        $this->assertGreaterThanOrEqual(3, $list->count());

        $a->delete();
        $b->delete();
        $c->delete();
        $foreign->delete();
    }

    /**
     * Each discussion surfaced in the group listing must resolve to a
     * /discussion/view/{guid} permalink. This is the link the listing item
     * view renders; if the route is unregistered getURL() collapses to the
     * site root and no thread permalinks appear.
     *
     * @return void
     */
    public function testEachListedDiscussionExposesViewPermalink(): void {
        $group = $this->createGroup(['owner_guid' => $this->user->guid]);

        $d = $this->makeDiscussion($group, 'Permalink topic');

        $collection = new GroupDiscussionsCollection($group);
        $items = $collection->getList()->get(50, 0);

        $this->assertNotEmpty($items, 'Group listing returned no discussions to link to');

        $found = false;
        foreach ($items as $entity) {
            if ((int) $entity->guid !== (int) $d->guid) {
                continue;
            }

            $found = true;

            $expected_path = '/discussion/view/' . $d->guid;
            $url = $entity->getURL();

            $this->assertStringContainsString(
                $expected_path,
                parse_url($url, PHP_URL_PATH) ?? '',
                'Discussion entity URL is not a /discussion/view permalink'
            );
        }

        $this->assertTrue($found, 'Created discussion was not present in its own group listing');

        $d->delete();
    }

    /**
     * The view:object:discussion route (the permalink target the listing
     * links to) must be registered and generate /discussion/view/{guid}.
     *
     * @return void
     */
    public function testViewRouteGeneratesThreadPermalink(): void {
        $group = $this->createGroup(['owner_guid' => $this->user->guid]);
        $d = $this->makeDiscussion($group, 'Route topic');

        $url = elgg_generate_url('view:object:discussion', [
            'guid' => $d->guid,
        ]);

        $this->assertStringContainsString('/discussion/view/' . $d->guid, $url);

        $d->delete();
    }

    /**
     * The listing's own canonical URL must be /discussion/group/{guid} so the
     * group tab links to a page that actually runs the group collection.
     *
     * @return void
     */
    public function testCollectionUrlIsGroupScoped(): void {
        $group = $this->createGroup(['owner_guid' => $this->user->guid]);

        $collection = new GroupDiscussionsCollection($group);

        $this->assertStringContainsString(
            '/discussion/group/' . $group->guid,
            $collection->getURL()
        );
    }
}
