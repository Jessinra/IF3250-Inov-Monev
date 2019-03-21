<?php

namespace Tests\Unit;

use Tests\TestCase;

class RoleControllerTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }


    public function testGETRoute()
    {

        $response = $this->get('/role');

        $response->assertStatus(200);
        $response->assertSee("Role management page");
    }


}
