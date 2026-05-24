<?php

namespace hypeJunction\Discussions;

use Elgg\Database\Seeds\Seed;

/**
 * Discussion entity seeder.
 */
class Seeder extends Seed {

	/**
	 * {@inheritDoc}
	 */
	public static function getType(): string {
		return 'discussion';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCountOptions(): array {
		return [
			'type' => 'object',
			'subtype' => 'discussion',
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function seed(): void {
		$this->advance($this->getCount());

		while ($this->seedsCount() < $this->getCount()) {
			$entity = new \hypeJunction\Discussion();
			$entity->owner_guid = $this->getRandomUser()->guid;
			$entity->container_guid = $entity->owner_guid;
			$entity->title = $this->faker->sentence(4);
			$entity->description = $this->faker->paragraph();
			$entity->access_id = ACCESS_PUBLIC;

			if (!$entity->save()) {
				continue;
			}

			$this->advance();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function unseed(): void {
		$entities = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'discussion',
			'metadata_name_value_pairs' => [
				[
					'name' => '__faker',
					'value' => true,
				],
			],
			'limit' => false,
			'batch' => true,
		]);

		foreach ($entities as $entity) {
			$entity->delete();
			$this->advance();
		}
	}

	/**
	 * Register this seed with the seeds list.
	 *
	 * @param \Elgg\Event $event 'seeds', 'database' event
	 * @return array
	 */
	public static function addSeed(\Elgg\Event $event) {
		$seeds = $event->getValue();
		$seeds[] = self::class;
		return $seeds;
	}
}
