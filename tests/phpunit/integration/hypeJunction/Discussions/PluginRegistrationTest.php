<?php

namespace hypeJunction\Discussions;

use Elgg\IntegrationTestCase;

/**
 * Lock in plugin registrations: routes, hooks, notifications.
 */
class PluginRegistrationTest extends IntegrationTestCase {

    public function up() {
    }

    public function down() {
    }

    /**
     * @return void
     */
    public function testPluginIsActive(): void {
        $plugin = elgg_get_plugin_from_id('hypediscussions');
        $this->assertNotNull($plugin);
        $this->assertTrue($plugin->isActive());
    }

    /**
     * @return void
     */
    public function testDiscussionRoutesRegistered(): void {
        $routes = _elgg_services()->routes;

        $this->assertNotNull($routes->get('add:object:discussion'));
        $this->assertNotNull($routes->get('edit:object:discussion'));
        $this->assertNotNull($routes->get('view:object:discussion'));
        $this->assertNotNull($routes->get('collection:object:discussion:all'));
        $this->assertNotNull($routes->get('collection:object:discussion:group'));
    }

    /**
     * @return void
     */
    public function testDiscussionWidgetRegistered(): void {
        $widgets = elgg_get_widget_types('profile');
        $this->assertArrayHasKey('discussion', $widgets);
    }

    /**
     * @return void
     */
    public function testPluginEventHandlersRegistered(): void {
        $events = _elgg_services()->events;
        $this->assertTrue(
            $events->hasHandler('container_logic_check', 'object'),
            'container_logic_check,object handler should be registered'
        );
        $this->assertTrue(
            $events->hasHandler('container_permissions_check', 'object'),
            'container_permissions_check,object handler should be registered'
        );
        $this->assertTrue(
            $events->hasHandler('permissions_check:comment', 'object'),
            'permissions_check:comment,object handler should be registered'
        );
        $this->assertTrue(
            $events->hasHandler('register', 'menu:site'),
            'menu:site handler should be registered'
        );
    }

    /**
     * @return void
     */
    public function testDiscussionViewRenders(): void {
        $user = $this->createUser();
        _elgg_services()->session_manager->setLoggedInUser($user);

        $group = $this->createGroup();

        $d = new \hypeJunction\Discussion();
        $d->owner_guid = $user->guid;
        $d->container_guid = $group->guid;
        $d->access_id = ACCESS_PUBLIC;
        $d->title = 'View Test';
        $d->description = 'body';
        $d->status = 'open';
        $this->assertNotFalse($d->save());

        $output = elgg_view('object/discussion', ['entity' => $d]);
        $this->assertIsString($output);

        _elgg_services()->session_manager->removeLoggedInUser();
        $d->delete();
    }
}
