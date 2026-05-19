<?php

return [
	'bootstrap' => \hypeJunction\Discussions\Bootstrap::class,

	'plugin' => [
		'version' => '7.0.0',
		'dependencies' => [
			'discussions' => [
				'position' => 'after',
			],
			'groups' => [
				'position' => 'after',
			],
		],
	],

	'entities' => [
		'discussion' => [
			'type' => 'object',
			'subtype' => 'discussion',
			'class' => \hypeJunction\Discussion::class,
		],
	],

	'capabilities' => [
		'searchable' => [
			'object:discussion' => true,
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

	'events' => [
		'route:rewrite' => [
			'discussions' => [
				\hypeJunction\Discussions\SetDiscussionRouteAlias::class => [],
			],
		],
		'uses:comments' => [
			'object:discussion' => [
				\Elgg\Values::class . '::getTrue' => [],
			],
		],
		'uses:cover' => [
			'object:discussion' => [
				\Elgg\Values::class . '::getTrue' => [],
			],
		],
		'uses:river' => [
			'object:discussion' => [
				\Elgg\Values::class . '::getTrue' => [],
			],
		],
		'allow_attachments' => [
			'object:blog' => [
				\Elgg\Values::class . '::getTrue' => [],
			],
		],
		'fields' => [
			'object:discussion' => [
				\hypeJunction\Discussions\AddDiscussionFields::class => [],
			],
			'object' => [
				\hypeJunction\Discussions\AddObjectFields::class => [],
			],
		],
		'register' => [
			'menu:site' => [
				\hypeJunction\Discussions\SiteMenu::class => [],
			],
			'menu:owner_block' => [
				\hypeJunction\Discussions\OwnerBlockMenu::class => [],
			],
			'menu:entity' => [
				\hypeJunction\Discussions\EntityMenu::class => [],
			],
		],
		'permissions_check:comment' => [
			'object' => [
				\hypeJunction\Discussions\CanThreadReplies::class => [],
				\hypeJunction\Discussions\CanCreateReply::class => [],
			],
		],
		'container_logic_check' => [
			'object' => [
				\hypeJunction\Discussions\CanContainReply::class => [],
			],
		],
		'container_permissions_check' => [
			'object' => [
				\hypeJunction\Discussions\CanCreateDiscussion::class => [],
			],
		],
		'prepare' => [
			'notification:publish:object:discussion' => [
				'discussion_prepare_notification' => [],
			],
		],
		'seeds' => [
			'database' => [
				\hypeJunction\Discussions\Seeder::class . '::addSeed' => [],
			],
		],
	],
];
