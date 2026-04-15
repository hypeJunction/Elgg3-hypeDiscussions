<?php

use hypeJunction\Discussions\RelatedDiscussionsCounter;

/**
 * Get total number of discussions related to an entity
 *
 * @param ElggEntity $entity Entity
 *
 * @return int|null
 */
function elgg_get_total_related_discussions(ElggEntity $entity) {
	if (!class_exists(\hypeJunction\Stash\Stash::class)) {
		return null;
	}
	return \hypeJunction\Stash\Stash::instance()->get(RelatedDiscussionsCounter::PROPERTY, $entity);
}
