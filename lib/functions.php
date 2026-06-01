<?php

use hypeJunction\Discussions\RelatedDiscussionsCounter;
use hypeJunction\Stash\Stash;

/**
 * Get total number of discussions related to an entity
 *
 * @param ElggEntity $entity Entity
 *
 * @return int
 */
function elgg_get_total_related_discussions(ElggEntity $entity) {
	if (!class_exists(Stash::class)) {
		return 0;
	}
	return Stash::instance()->get(RelatedDiscussionsCounter::PROPERTY, $entity);
}
