<?php

if (!\hypeJunction\Capabilities\Roles::can('read', 'post_discussions')) {
	throw new \Elgg\PageNotFoundException();
}

$request = elgg_extract('request', $vars);
/* @var $request \Elgg\Request */

$guid = $request->getParam('guid');
elgg_entity_gatekeeper($guid);

$entity = get_entity($guid);

elgg_push_entity_breadcrumbs($entity);

$collections = elgg()->collections;
/* @var $collections \hypeJunction\Lists\Collections */

$collection = $collections->build($request->getRoute(), $entity, $request->getParams());
/* @var $collection \hypeJunction\Lists\CollectionInterface */

if (!$collection) {
	throw new \Elgg\PageNotFoundException();
}

$content = elgg_view('collection/view', [
	'collection' => $collection,
]);

if ($request->isXhr()) {
	echo $content;
	return;
}

$type = $collection->getType();
$subtypes = (array) $collection->getSubtypes();
$subtype = array_shift($subtypes);

foreach ($collection->getMenu() as $item) {
	$item->addLinkClass('elgg-button elgg-button-action');
	elgg_register_menu_item('title', $item);
}

$layout = elgg_view_layout('default', [
	'collection' => $collection,
	'title' => $collection->getDisplayName(),
	'content' => $content,
	'sidebar' => elgg_view('collection/sidebar', [
		'collection' => $collection,
	]),
	'filter_id' => 'collection/group',
	'filter_value' => 'all',
	'target' => $entity,
]);

echo elgg_view_page($title, $layout, 'default', [
	'collection' => $collection,
]);