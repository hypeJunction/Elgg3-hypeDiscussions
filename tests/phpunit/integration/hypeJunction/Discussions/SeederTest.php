<?php

namespace hypeJunction\Discussions;

use Elgg\Event;
use Elgg\IntegrationTestCase;

/**
 * Regression guard for the Seed subclass (commits 0fda58b / 983477): the seeder
 * must satisfy the Elgg 6.1+ Seed contract (static getType() + getCountOptions())
 * and register itself onto the seeds,database list array.
 */
class SeederTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    /**
     * @return void
     */
    public function testGetTypeReturnsDiscussion(): void {
        $this->assertSame('discussion', Seeder::getType());
    }

    /**
     * @return void
     */
    public function testAddSeedAppendsSelfToSeedsList(): void {
        $event = new Event(elgg(), 'seeds', 'database', ['SomeOther\\Seed'], []);

        $out = Seeder::addSeed($event);

        $this->assertIsArray($out);
        $this->assertContains(Seeder::class, $out);
        // Existing seeds must be preserved, not clobbered.
        $this->assertContains('SomeOther\\Seed', $out);
    }
}
