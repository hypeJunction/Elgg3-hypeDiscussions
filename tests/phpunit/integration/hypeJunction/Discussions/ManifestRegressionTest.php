<?php

namespace hypeJunction\Discussions;

use Elgg\IntegrationTestCase;

/**
 * Static regression guards over elgg-plugin.php that lock in migration fixes
 * which are expressed in the manifest itself:
 *   - 4f70390: lib/functions.php required from the TOP (before `return [`) so
 *     elgg_get_total_related_discussions() is defined (autoload.files is too late
 *     for the git-tracked helper).
 *   - b3a9a92: events use the 7.x callable-as-key format (handler class => []).
 *   - f03b9e1: the publish notification is wired under the
 *     notification:publish:object:discussion event key, not the legacy create key.
 */
class ManifestRegressionTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    /**
     * @return string
     */
    protected function manifestPath(): string {
        return dirname(__DIR__, 5) . '/elgg-plugin.php';
    }

    /**
     * @return void
     */
    public function testLibFunctionsRequiredFromManifestTop(): void {
        $src = file_get_contents($this->manifestPath());

        $requirePos = strpos($src, "require_once __DIR__ . '/lib/functions.php';");
        $returnPos = strpos($src, 'return [');

        $this->assertNotFalse(
            $requirePos,
            'elgg-plugin.php must require lib/functions.php so global helpers are defined at boot'
        );
        $this->assertNotFalse($returnPos);
        $this->assertLessThan(
            $returnPos,
            $requirePos,
            'require_once lib/functions.php must precede the return array (autoload.files loads it too late)'
        );
    }

    /**
     * @return void
     */
    public function testEventsUseCallableKeyFormat(): void {
        $manifest = require $this->manifestPath();

        $this->assertArrayHasKey('events', $manifest);
        $rewrite = $manifest['events']['route:rewrite']['discussions'] ?? null;

        $this->assertIsArray($rewrite);
        $this->assertArrayHasKey(
            SetDiscussionRouteAlias::class,
            $rewrite,
            'handlers must be registered as callable-key => [] (7.x events format), not a legacy value list'
        );
    }

    /**
     * @return void
     */
    public function testPublishNotificationRegisteredNotCreate(): void {
        $manifest = require $this->manifestPath();

        $prepare = $manifest['events']['prepare'] ?? [];

        $this->assertArrayHasKey(
            'notification:publish:object:discussion',
            $prepare,
            'notification prepare handler must key off the publish action (f03b9e1)'
        );
        $this->assertArrayNotHasKey(
            'notification:create:object:discussion',
            $prepare,
            'legacy create-notification key must not be reintroduced'
        );
    }
}
