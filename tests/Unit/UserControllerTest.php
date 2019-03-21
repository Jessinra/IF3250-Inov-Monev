<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }


    public function testGETRoute()
    {

        $response = $this->get('/user');

        $response->assertStatus(200);
        $response->assertSee("User management page");
    }

}
