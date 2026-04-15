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

    public function testPluginIsActive(): void {
        $plugin = elgg_get_plugin_from_id('hypediscussions');
        $this->assertNotNull($plugin);
        $this->assertTrue($plugin->isActive());
    }

    public function testDiscussionRoutesRegistered(): void {
        $routes = _elgg_services()->routes;

        $this->assertNotNull($routes->get('add:object:discussion'));
        $this->assertNotNull($routes->get('edit:object:discussion'));
        $this->assertNotNull($routes->get('view:object:discussion'));
        $this->assertNotNull($routes->get('collection:object:discussion:all'));
        $this->assertNotNull($routes->get('collection:object:discussion:group'));
    }

    public function testDiscussionWidgetRegistered(): void {
        $widgets = elgg_get_widget_types('profile');
        $this->assertArrayHasKey('discussion', $widgets);
    }

    public function testPluginHookHandlersRegistered(): void {
        $hooks = _elgg_services()->hooks;
        $this->assertTrue(
            $hooks->hasHandler('container_logic_check', 'object'),
            'container_logic_check,object handler should be registered'
        );
        $this->assertTrue(
            $hooks->hasHandler('container_permissions_check', 'object'),
            'container_permissions_check,object handler should be registered'
        );
        $this->assertTrue(
            $hooks->hasHandler('permissions_check:comment', 'object'),
            'permissions_check:comment,object handler should be registered'
        );
        $this->assertTrue(
            $hooks->hasHandler('register', 'menu:site'),
            'menu:site handler should be registered'
        );
    }

    public function testDiscussionViewRenders(): void {
        $user = $this->createUser();
        $group = $this->createGroup();

        $d = new \hypeJunction\Discussion();
        $d->owner_guid = $user->guid;
        $d->container_guid = $group->guid;
        $d->access_id = ACCESS_PUBLIC;
        $d->title = 'View Test';
        $d->description = 'body';
        $d->status = 'open';
        $d->save();

        $output = elgg_view('object/discussion', ['entity' => $d]);
        $this->assertIsString($output);

        $d->delete();
    }
}
