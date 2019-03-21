<?php

namespace Tests\Unit;

use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }


    public function testGETRoute()
    {

        $response = $this->get('/group');

        $response->assertStatus(200);
        $response->assertSee("Group management page");
    }

}
