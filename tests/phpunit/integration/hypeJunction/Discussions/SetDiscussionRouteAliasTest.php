<?php

namespace hypeJunction\Discussions;

use Elgg\HooksRegistrationService\Hook;
use Elgg\IntegrationTestCase;

/**
 * Lock in behavior of the route:rewrite,discussions alias
 * which aliases 'discussions/...' to 'discussion/...'.
 */
class SetDiscussionRouteAliasTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    public function testAliasesDiscussionsIdentifier(): void {
        $hook = new Hook(elgg(), 'route:rewrite', 'discussions', [
            'identifier' => 'discussions',
            'segments' => ['view', '123'],
        ], []);

        $handler = new SetDiscussionRouteAlias();
        $out = $handler($hook);

        $this->assertEquals('discussion', $out['identifier']);
        $this->assertEquals(['view', '123'], $out['segments']);
    }

    public function testDefaultsEmptySegmentsToAll(): void {
        $hook = new Hook(elgg(), 'route:rewrite', 'discussions', [
            'identifier' => 'discussions',
            'segments' => [],
        ], []);

        $handler = new SetDiscussionRouteAlias();
        $out = $handler($hook);

        $this->assertEquals('discussion', $out['identifier']);
        $this->assertEquals(['all'], $out['segments']);
    }

    public function testLeavesOtherIdentifiersAlone(): void {
        $hook = new Hook(elgg(), 'route:rewrite', 'other', [
            'identifier' => 'other',
            'segments' => ['foo'],
        ], []);

        $handler = new SetDiscussionRouteAlias();
        $out = $handler($hook);

        $this->assertEquals('other', $out['identifier']);
    }
}
