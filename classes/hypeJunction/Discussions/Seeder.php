<?php

namespace hypeJunction\Discussions;

use Elgg\Database\Seeds\Seed;

class Seeder extends Seed {

	public function seed() {
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

	public function unseed() {
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

	public static function addSeed(\Elgg\Event $event) {
		$seeds = $event->getValue();
		$seeds[] = self::class;
		return $seeds;
	}
}
