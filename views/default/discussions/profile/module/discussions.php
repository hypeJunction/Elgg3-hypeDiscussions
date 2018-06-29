<?php

if (!elgg_get_plugin_setting('post_discussions', 'hypeDiscussions')) {
	return;
}

if (!\hypeJunction\Capabilities\Roles::can('read', 'post_discussions')) {
	return;
}

$entity = elgg_extract('entity', $vars);
if (!$entity->enable_discussions) {
	return;
}

if (!elgg_get_total_related_discussions($entity)) {
	return;
}

$collection = elgg_get_collection('collection:object:discussion:post', $entity);


$count = $collection->getList()->count();

$cotnent = '';
$footer = '';

if (empty($count)) {
	$content = elgg_format_element('p', [
		'class' => 'elgg-no-results',
	], elgg_echo('collection:no_results'));
} else {
	$content = $collection->render(['limit' => 4]);

	$items = $collection->getMenu();

	$items[] = ElggMenuItem::factory([
		'name' => 'all',
		'href' => $collection->getURL(),
		'text' => elgg_echo('collection:more'),
		'icon_alt' => 'caret-right',
	]);

	$menu = elgg_view_menu('widget:more', [
		'entity' => $widget,
		'collection' => $collection,
		'items' => $items,
		'class' => 'elgg-menu-hz elgg-menu-entity',
	]);

	if ($menu) {
		$footer = elgg_format_element('div', [
			'class' => 'elgg-widget-more',
		], $menu);
	}
}

echo elgg_view_module('info', elgg_echo('collection:object:discussion'), $content, [
	'footer' => $footer,
	'class' => 'has-list',
]);