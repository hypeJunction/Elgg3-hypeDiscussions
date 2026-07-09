<?php

namespace hypeJunction\Discussions;

use Elgg\Event;
use Elgg\IntegrationTestCase;
use ElggMenuItem;

/**
 * Regression guard for the 7.x menu-register array-value contract
 * (commits 64568c5 / 9b5a539): SiteMenu/OwnerBlockMenu/EntityMenu must append
 * with $menu[] = ElggMenuItem::factory(...) and RETURN the array. Under the old
 * MenuItems->add() idiom the returned value would be null and the item silently
 * missing.
 */
class MenuRegistrationTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
        _elgg_services()->session_manager->removeLoggedInUser();
        $plugin = elgg_get_plugin_from_id('hypediscussions');
        if ($plugin) {
            $plugin->unsetSetting('site_wide_discussions');
            $plugin->unsetSetting('post_discussions');
        }
    }

    /**
     * @param array $items
     * @return array
     */
    protected function names(array $items): array {
        return array_map(static function ($item) {
            return $item instanceof ElggMenuItem ? $item->getName() : null;
        }, $items);
    }

    /**
     * @return void
     */
    public function testSiteMenuAppendsDiscussionItemAndReturnsArray(): void {
        $event = new Event(elgg(), 'register', 'menu:site', [], []);

        $out = (new SiteMenu())($event);

        $this->assertIsArray($out);
        $this->assertContains('discussion', $this->names($out));
    }

    /**
     * @return void
     */
    public function testOwnerBlockMenuLeavesMenuUnchangedForNonUser(): void {
        $object = $this->createObject(['subtype' => 'blog']);

        $event = new Event(elgg(), 'register', 'menu:owner_block', [], [
            'entity' => $object,
        ]);

        $out = (new OwnerBlockMenu())($event);

        $this->assertSame([], $out);
    }

    /**
     * @return void
     */
    public function testOwnerBlockMenuAppendsForUserWhenSiteWideEnabled(): void {
        $plugin = elgg_get_plugin_from_id('hypediscussions');
        $plugin->setSetting('site_wide_discussions', '1');

        $user = $this->createUser();

        $event = new Event(elgg(), 'register', 'menu:owner_block', [], [
            'entity' => $user,
        ]);

        $out = (new OwnerBlockMenu())($event);

        $this->assertIsArray($out);
        $this->assertContains('discussions', $this->names($out));
    }

    /**
     * @return void
     */
    public function testEntityMenuLeavesMenuUnchangedWhenPostDiscussionsDisabled(): void {
        $plugin = elgg_get_plugin_from_id('hypediscussions');
        $plugin->unsetSetting('post_discussions');

        $object = $this->createObject(['subtype' => 'blog']);
        $object->enable_discussions = true;

        $event = new Event(elgg(), 'register', 'menu:entity', [], [
            'entity' => $object,
        ]);

        $out = (new EntityMenu())($event);

        $this->assertSame([], $out);
    }
}
