<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggDiscussion) {
	return;
}

$full_view = elgg_extract('full_view', $vars);

if ($entity->discussed_post_guid) {
	$discussed = get_entity($entity->discussed_post_guid);

	if ($discussed) {
		$vars['imprint'][] = [
			'icon_name' => 'link',
			'content' => elgg_view('output/url', [
				'href' => $discussed->getURL(),
				'text' => $discussed->getDisplayName(),
			]),
			'class' => 'elgg-state elgg-state-notice',
		];

		if ($full_view) {
			$view = elgg_view_entity($discussed, [
				'full_view' => false,
				'show_responses' => false,
			]);

			$vars['attachments'] = elgg_view_module('aside', elgg_echo('discussion:relates_to'), $view);
		}
	} else {
		$vars['imprint'][] = [
			'icon_name' => 'link',
			'content' => elgg_echo('discussion:post:no_access'),
			'class' => 'elgg-state elgg-state-error',
		];

		if ($full_view) {
			$view = elgg_view_message('error', elgg_echo('discussion:post:no_access:error'), [
				'title' => elgg_echo('discussion:post:no_access'),
			]);

			$vars['attachments'] = elgg_view_module('aside', elgg_echo('discussion:relates_to'), $view);
		}
	}

}

if ($entity->status && $entity->status !== 'open') {
	$vars['imprint'][] = [
		'icon_name' => 'ban',
		'content' => elgg_echo("status:{$entity->status}"),
		'class' => 'elgg-listing-discussion-status elgg-state elgg-state-danger',
	];

	if ($full_view && elgg_extract('show_responses', $vars) !== false) {
		$responses = elgg_view('discussion/closed');
		$responses .= elgg_view_comments($entity, false, [
			'full_view' => true,
		]);

		$vars['responses'] = $responses;
	}
}

$comment_text = '';

$last_comment = elgg_get_last_comment($entity);
if ($last_comment) {
	$poster = $last_comment->getOwnerEntity();
	$comment_time = elgg_view_friendly_time($last_comment->time_created);

	$comment_text = elgg_view('output/url', [
		'text' => elgg_echo('discussion:updated', [$poster->getDisplayName(), $comment_time]),
		'href' => $last_comment->getURL(),
	]);

	$comment_text = elgg_format_element('div', [], $comment_text);
}

$by_line = elgg_view('object/elements/imprint', $vars);

$vars['subtitle'] = "$by_line $comment_text";

echo elgg_view('post/view', $vars);