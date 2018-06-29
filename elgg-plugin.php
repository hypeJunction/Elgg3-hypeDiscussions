<?php

return [
	'bootstrap' => \hypeJunction\Discussions\Bootstrap::class,

	'entities' => [
		'discussion' => [
			'type' => 'object',
			'subtype' => 'discussion',
			'class' => \hypeJunction\Discussion::class,
			'searchable' => true,
		],
	],

	'routes' => [
		'add:object:discussion' => [
			'path' => '/discussion/add/{guid}',
			'resource' => 'post/add',
			'defaults' => [
				'type' => 'object',
				'subtype' => 'discussion',
			],
		],
		'edit:object:discussion' => [
			'path' => '/discussion/edit/{guid}',
			'resource' => 'post/edit',
		],
		'view:object:discussion' => [
			'path' => '/discussion/view/{guid}/{title?}',
			'resource' => 'post/view',
		],
		'collection:object:discussion:all' => [
			'path' => '/discussion/all',
			'resource' => 'collection/all',
		],
		'collection:object:discussion:owner' => [
			'path' => '/discussion/owner/{username?}',
			'resource' => 'collection/owner',
		],
		'collection:object:discussion:friends' => [
			'path' => '/discussion/friends/{username?}',
			'resource' => 'collection/friends',
		],
		'collection:object:discussion:group' => [
			'path' => '/discussion/group/{guid}',
			'resource' => 'collection/group',
		],
		'collection:object:discussion:post' => [
			'path' => '/discussion/post/{guid}',
			'resource' => 'discussions/post',
		],
	],

	'widgets' => [
		'discussion' => [
			'context' => ['profile', 'dashboard', 'groups'],
		],
	],
];