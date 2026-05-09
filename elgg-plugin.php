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
				[
					'handler' => \hypeJunction\Discussions\SetDiscussionRouteAlias::class,
				],
			],
		],
		'uses:comments' => [
			'object:discussion' => [
				[
					'handler' => [\Elgg\Values::class, 'getTrue'],
				],
			],
		],
		'uses:cover' => [
			'object:discussion' => [
				[
					'handler' => [\Elgg\Values::class, 'getTrue'],
				],
			],
		],
		'uses:river' => [
			'object:discussion' => [
				[
					'handler' => [\Elgg\Values::class, 'getTrue'],
				],
			],
		],
		'allow_attachments' => [
			'object:blog' => [
				[
					'handler' => [\Elgg\Values::class, 'getTrue'],
				],
			],
		],
		'fields' => [
			'object:discussion' => [
				[
					'handler' => \hypeJunction\Discussions\AddDiscussionFields::class,
				],
			],
			'object' => [
				[
					'handler' => \hypeJunction\Discussions\AddObjectFields::class,
				],
			],
		],
		'register' => [
			'menu:site' => [
				[
					'handler' => \hypeJunction\Discussions\SiteMenu::class,
				],
			],
			'menu:owner_block' => [
				[
					'handler' => \hypeJunction\Discussions\OwnerBlockMenu::class,
				],
			],
			'menu:entity' => [
				[
					'handler' => \hypeJunction\Discussions\EntityMenu::class,
				],
			],
		],
		'permissions_check:comment' => [
			'object' => [
				[
					'handler' => \hypeJunction\Discussions\CanThreadReplies::class,
				],
				[
					'handler' => \hypeJunction\Discussions\CanCreateReply::class,
				],
			],
		],
		'container_logic_check' => [
			'object' => [
				[
					'handler' => \hypeJunction\Discussions\CanContainReply::class,
				],
			],
		],
		'container_permissions_check' => [
			'object' => [
				[
					'handler' => \hypeJunction\Discussions\CanCreateDiscussion::class,
				],
			],
		],
		'prepare' => [
			'notification:publish:object:discussion' => [
				[
					'handler' => 'discussion_prepare_notification',
				],
			],
		],
	],
];
