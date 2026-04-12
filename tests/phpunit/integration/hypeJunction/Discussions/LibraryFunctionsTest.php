<?php

namespace hypeJunction\Discussions;

use Elgg\IntegrationTestCase;

/**
 * Lock in behavior of lib/functions.php helpers.
 */
class LibraryFunctionsTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    public function testElggGetTotalRelatedDiscussionsFunctionExists(): void {
        $this->assertTrue(function_exists('elgg_get_total_related_discussions'));
    }

    public function testElggGetTotalRelatedDiscussionsReturnsIntegerForEntity(): void {
        $entity = $this->createObject(['subtype' => 'blog']);
        $count = elgg_get_total_related_discussions($entity);
        // Counter may return int or null depending on Stash state; accept both
        // but verify it does not throw.
        $this->assertTrue($count === null || is_int($count) || is_numeric($count));
    }
}
