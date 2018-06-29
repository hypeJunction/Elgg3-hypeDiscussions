<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('discussion:settings:site_wide_discussions'),
	'#help' => elgg_echo('discussion:settings:site_wide_discussions:help'),
	'name' => 'params[site_wide_discussions]',
	'value' => $entity->site_wide_discussions,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('discussion:settings:post_discussions'),
	'#help' => elgg_echo('discussion:settings:post_discussions:help'),
	'name' => 'params[post_discussions]',
	'value' => $entity->post_discussions,
	'options_values' => [
		0 => elgg_echo('option:no'),
		1 => elgg_echo('option:yes'),
	],
]);