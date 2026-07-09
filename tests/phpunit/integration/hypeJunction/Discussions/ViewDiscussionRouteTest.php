<?php

namespace hypeJunction\Discussions;

use Elgg\Exceptions\Http\PageNotFoundException;
use Elgg\IntegrationTestCase;

/**
 * Regression: single-discussion view route (/discussion/view/{guid}/{title?})
 * returned HTTP 404 on Elgg 7.x even for admins.
 *
 * Root cause: hypediscussions/elgg-plugin.php overrides the core
 * 'view:object:discussion' route with resource => 'post/view', but no
 * 'resources/post/view' view exists in core or the plugin. elgg_view_resource()
 * logs "The view resources/post/view is missing." and throws
 * PageNotFoundException, so the reply-thread page is dead. Core discussions
 * ships resource 'discussion/view' (resources/discussion/view) instead.
 *
 * These tests fail while the route points at a non-existent resource view and
 * pass once it points at an existing one.
 */
class ViewDiscussionRouteTest extends IntegrationTestCase {

	public function up() {
	}

	public function down() {
	}

	/**
	 * The resource backing 'view:object:discussion' must resolve to a real
	 * resource view; otherwise every single-discussion page 404s.
	 *
	 * @return void
	 */
	public function testViewRouteResourceViewExists(): void {
		$route = _elgg_services()->routes->get('view:object:discussion');
		$this->assertNotNull($route, 'view:object:discussion route must be registered');

		$resource = $route->getDefault('_resource');
		$this->assertNotEmpty($resource, 'view:object:discussion must declare a resource');

		$this->assertTrue(
			elgg_view_exists("resources/{$resource}"),
			"view:object:discussion resource '{$resource}' has no resources/{$resource} view; "
			. 'elgg_view_resource() will throw PageNotFoundException (HTTP 404) for every discussion page'
		);
	}

	/**
	 * Reproduce the e2e failure at the PHP layer: rendering the discussion view
	 * resource for a real, gatekeeper-passing entity must not 404.
	 *
	 * @return void
	 */
	public function testDiscussionViewResourceDoesNotThrowPageNotFound(): void {
		$user = $this->createUser();
		_elgg_services()->session_manager->setLoggedInUser($user);

		$group = $this->createGroup();

		$discussion = new \hypeJunction\Discussion();
		$discussion->owner_guid = $user->guid;
		$discussion->container_guid = $group->guid;
		$discussion->access_id = ACCESS_PUBLIC;
		$discussion->title = 'Route View Test';
		$discussion->description = 'body';
		$discussion->status = 'open';
		$this->assertNotFalse($discussion->save());

		$route = _elgg_services()->routes->get('view:object:discussion');
		$resource = $route->getDefault('_resource');

		try {
			$output = elgg_view_resource($resource, [
				'guid' => $discussion->guid,
				'entity' => $discussion,
			]);
		} catch (PageNotFoundException $e) {
			_elgg_services()->session_manager->removeLoggedInUser();
			$discussion->delete();
			$this->fail(
				"view:object:discussion resource '{$resource}' 404s: resources/{$resource} view is missing "
				. '— single discussion pages and reply threads are unreachable'
			);
		}

		_elgg_services()->session_manager->removeLoggedInUser();
		$discussion->delete();

		$this->assertIsString($output);
		$this->assertNotEmpty($output, 'discussion view resource rendered empty output');
	}
}
