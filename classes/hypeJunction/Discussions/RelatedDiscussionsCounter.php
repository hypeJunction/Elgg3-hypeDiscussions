<?php

namespace hypeJunction\Discussions;

use Elgg\Event;
use Elgg\EventsService;
use Elgg\PluginHooksService;
use ElggComment;
use hypeJunction\Discussion;
use hypeJunction\Stash\Preloader;
use hypeJunction\Stash\Stash;

class RelatedDiscussionsCounter implements Preloader {

	const PROPERTY = 'related_discussions_total';

	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return self::PROPERTY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority() {
		return 500;
	}

	/**
	 * {@inheritdoc}
	 */
	public function up(Stash $stash, EventsService $events, PluginHooksService $hooks) {
		$callback = function (Event $event) use ($stash) {
			elgg_call(
				ELGG_IGNORE_ACCESS,
				function () use ($event, $stash) {
					$discussion = $event->getObject();
					if (!$discussion instanceof \ElggDiscussion) {
						return;
					}

					$discussed_post_guid = $discussion->discussed_post_guid;
					if (!$discussed_post_guid) {
						return;
					}

					$entity = get_entity($discussed_post_guid);
					if (!$entity) {
						return;
					}

					$stash->get(self::PROPERTY, $entity, true);
				}
			);
		};

		$events->registerHandler('create', 'object', $callback);
		$events->registerHandler('delete:after', 'object', $callback);
	}

	/**
	 * {@inheritdoc}
	 */
	public function preload(\ElggEntity $entity) {
		return elgg_call(
			ELGG_IGNORE_ACCESS,
			function () use ($entity) {
				return elgg_get_entities([
					'types' => 'object',
					'subtypes' => Discussion::SUBTYPE,
					'metadata_name_value_pairs' => [
						'discussed_post_guid' => (int) $entity->guid,
					],
					'count' => true,
				]);
			}
		);
	}
}