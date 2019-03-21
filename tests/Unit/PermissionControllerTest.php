<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function testGETRoute()
    {

        $response = $this->get('/permission');

        $response->assertStatus(200);
        $response->assertSee("Permission management page");
    }

    public function testPOSTRouteWithoutAction()
    {

        $response = $this->call('POST', '/permission', array());

        $response->assertStatus(200);
        $response->assertSee("abort 404");
    }

    public function testPOSTRouteWithAction()
    {

        $response = $this->call('POST', '/permission', array(
            'action' => 'test only',
        ));

        $response->assertStatus(200);
        $response->assertSee("Permission management page");
    }

    public function testCreatePermission()
    {

        $response = $this->call('POST', '/permission', array(
            'action' => 'create',
            'name' => 'test permission',
            'description' => 'permission for test only'
        ));

        $this->assertDatabaseHas('permissions', [
            'name' => 'test permission',
            'description' => 'permission for test only'
        ]);

    }

    public function testCreatePermissionWithoutName()
    {
        /*
            Creating permission without name is not allowed
        */

        $response = $this->call('POST', '/permission', array(
            'action' => 'create',
            'description' => 'permission for test only'
        ));
        $response->assertSee("Permission cannot be created!");

        $this->assertDatabaseMissing('permissions', [
            'description' => 'permission for test only'
        ]);
    }

    public function testCreatePermissionWithoutDescription()
    {

        $response = $this->call('POST', '/permission', array(
            'action' => 'create',
            'name' => 'test permission',
        ));
        $response->assertSee("Permission cannot be created!");

        $this->assertDatabaseMissing('permissions', [
            'name' => 'test permission',
        ]);

    }
}
