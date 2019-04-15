<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WorkflowControllerTest extends TestCase
{
    use RefreshDatabase;

    private $retrieveAction = "retrieve";
    private $connectAction = "connect_stage";
    private $disconnectAction = "disconnect_stage";

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();          // show error stacktrace
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('stages')->truncate();   // reset id increment
        DB::table('workflow_next')->truncate();   // reset id increment
        DB::table('workflow_prev')->truncate();   // reset id increment
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->setOutputCallback(function () {      // hide echos
        });

    }

    /*===========================================
                    Routing section
     ===========================================*/

    public function testGETRoute()
    {

        $response = $this->get('/api/workflow');

        $response->assertStatus(200);
//        $response->assertSee("Stage management page");
    }

    public function testPOSTRouteWithoutAction()
    {

        $response = $this->call('POST', '/api/workflow', array());

        $response->assertStatus(200);
        $response->assertSee("abort 404");
    }

    public function testPOSTRouteWithAction()
    {

        $response = $this->call('POST', '/api/workflow', array(
            'action' => 'test only',
        ));

        $response->assertStatus(200);
        $response->assertSee("Workflow management page");
    }

//    TODO: Test authorized


    public function testConnect()
    {

        $this->createDummyStage(1);
        $this->createDummyStage(2);

        $this->call('POST', '/api/workflow', array(
            'action' => $this->connectAction,
            'id' => "1",
            'next_id' => "2",
        ));

        $this->assertDatabaseHas('workflow_next', [
            'id' => 1,
            'current_id' => "1",
            'next_id' => "2",
        ]);

        $this->assertDatabaseHas('workflow_prev', [
            'id' => 1,
            'current_id' => "2",
            'prev_id' => "1",
        ]);
    }

    public function testConnectCircular()
    {

        $this->createDummyStage();

        $this->call('POST', '/api/workflow', array(
            'action' => $this->connectAction,
            'id' => "1",
            'next_id' => "1",
        ));

        $this->assertDatabaseHas('workflow_next', [
            'id' => 1,
            'current_id' => "1",
            'next_id' => "1",
        ]);

        $this->assertDatabaseHas('workflow_prev', [
            'id' => 1,
            'current_id' => "1",
            'prev_id' => "1",
        ]);
    }

    public function testConnectBranch()
    {

        $this->createDummyStage(1);
        $this->createDummyStage(2);
        $this->createDummyStage(3);

        $this->call('POST', '/api/workflow', array(
            'action' => $this->connectAction,
            'id' => "1",
            'next_id' => "2",
        ));

        $this->call('POST', '/api/workflow', array(
            'action' => $this->connectAction,
            'id' => "1",
            'next_id' => "3",
        ));

        $this->assertDatabaseHas('workflow_next', [
            'id' => 1,
            'current_id' => "1",
            'next_id' => "2",
        ]);

        $this->assertDatabaseHas('workflow_prev', [
            'id' => 1,
            'current_id' => "2",
            'prev_id' => "1",
        ]);

        $this->assertDatabaseHas('workflow_next', [
            'id' => 2,
            'current_id' => "1",
            'next_id' => "3",
        ]);

        $this->assertDatabaseHas('workflow_prev', [
            'id' => 2,
            'current_id' => "3",
            'prev_id' => "1",
        ]);
    }

    public function testConnectNonExist()
    {
        $this->createDummyStage();

        $this->call('POST', '/api/workflow', array(
            'action' => $this->connectAction,
            'id' => "1",
            'next_id' => "2",
        ));

        $this->assertDatabaseMissing('workflow_next', [
            'id' => 1,
            'current_id' => "1",
            'next_id' => "2",
        ]);

        $this->assertDatabaseMissing('workflow_prev', [
            'id' => 1,
            'current_id' => "2",
            'prev_id' => "1",
        ]);
    }

    public function testConnectFromNonExist()
    {
        $this->createDummyStage();

        $this->call('POST', '/api/workflow', array(
            'action' => $this->connectAction,
            'id' => "2",
            'next_id' => "1",
        ));

        $this->assertDatabaseMissing('workflow_next', [
            'id' => 1,
            'current_id' => "2",
            'next_id' => "1",
        ]);

        $this->assertDatabaseMissing('workflow_prev', [
            'id' => 1,
            'current_id' => "1",
            'prev_id' => "2",
        ]);
    }

    public function testDisconnect()
    {
        $this->createDummyWorkflow();

        $this->call('POST', '/api/workflow', array(
            'action' => $this->disconnectAction,
            'id' => "1",
            'next_id' => "2",
        ));

        $this->assertDatabaseMissing('workflow_next', [
            'id' => 1,
            'current_id' => "1",
            'next_id' => "2",
        ]);

        $this->assertDatabaseMissing('workflow_prev', [
            'id' => 1,
            'current_id' => "2",
            'prev_id' => "1",
        ]);
    }

    public function testDisconnectNoConnection()
    {
        $this->createDummyWorkflow();

        $this->call('POST', '/api/workflow', array(
            'action' => $this->disconnectAction,
            'id' => "1",
            'next_id' => "2",
        ));

        $this->assertDatabaseMissing('workflow_next', [
            'id' => 1,
            'current_id' => "1",
            'next_id' => "2",
        ]);

        $this->assertDatabaseMissing('workflow_prev', [
            'id' => 1,
            'current_id' => "2",
            'prev_id' => "1",
        ]);

        $this->call('POST', '/api/workflow', array(
            'action' => $this->disconnectAction,
            'id' => "1",
            'next_id' => "2",
        ));

        $this->assertDatabaseMissing('workflow_next', [
            'id' => 1,
            'current_id' => "1",
            'next_id' => "2",
        ]);

        $this->assertDatabaseMissing('workflow_prev', [
            'id' => 1,
            'current_id' => "2",
            'prev_id' => "1",
        ]);
    }

    public function testDisconnectCircular()
    {
        $this->createDummyWorkflow();

        $this->call('POST', '/api/workflow', array(
            'action' => $this->disconnectAction,
            'id' => "1",
            'next_id' => "1",
        ));

        $this->assertDatabaseMissing('workflow_next', [
            'id' => 2,
            'current_id' => "1",
            'next_id' => "1",
        ]);

        $this->assertDatabaseMissing('workflow_prev', [
            'id' => 2,
            'current_id' => "1",
            'prev_id' => "1",
        ]);
    }

    public function testDisconnectBranch()
    {
        $this->createDummyWorkflow();

        $this->call('POST', '/api/workflow', array(
            'action' => $this->disconnectAction,
            'id' => "2",
            'next_id' => "3",
        ));

        $this->assertDatabaseMissing('workflow_next', [
            'id' => 3,
            'current_id' => "2",
            'next_id' => "3",
        ]);

        $this->assertDatabaseMissing('workflow_prev', [
            'id' => 3,
            'current_id' => "3",
            'prev_id' => "2",
        ]);

        $this->assertDatabaseHas('workflow_next', [
            'id' => 4,
            'current_id' => "2",
            'next_id' => "4",
        ]);

        $this->assertDatabaseHas('workflow_prev', [
            'id' => 4,
            'current_id' => "4",
            'prev_id' => "2",
        ]);
    }

    public function testDisconnectNonExist()
    {
        $this->createDummyWorkflow();

        $this->call('POST', '/api/workflow', array(
            'action' => $this->disconnectAction,
            'id' => "2",
            'next_id' => "5",
        ));

        $this->assertDatabaseMissing('workflow_next', [
            'current_id' => "2",
            'next_id' => "5",
        ]);

        $this->assertDatabaseMissing('workflow_prev', [
            'current_id' => "5",
            'prev_id' => "2",
        ]);
    }

    public function testDisconnectFromNonExist()
    {
        $this->createDummyWorkflow();

        $this->call('POST', '/api/workflow', array(
            'action' => $this->disconnectAction,
            'id' => "5",
            'next_id' => "2",
        ));

        $this->assertDatabaseMissing('workflow_next', [
            'current_id' => "5",
            'next_id' => "2",
        ]);

        $this->assertDatabaseMissing('workflow_prev', [
            'current_id' => "2",
            'prev_id' => "5",
        ]);
    }

    public function testRetrieve()
    {
        $this->createDummyWorkflow();

        $response = $this->call('POST', '/api/workflow', array(
            'action' => $this->retrieveAction,
        ));

        $workflow = $response->json();
        $this->assertEquals('1', $workflow[0]["this"]['id']);
        $this->assertEquals('2', $workflow[0]['next'][0]['id']);
        $this->assertEquals('1', $workflow[0]['prev'][0]['id']);

        $this->assertEquals('2', $workflow[1]['this']['id']);
        $this->assertEquals('3', $workflow[1]['next'][0]['id']);
        $this->assertEquals('4', $workflow[1]['next'][1]['id']);
        $this->assertEquals('1', $workflow[1]['prev'][0]['id']);

        $this->assertEquals('3', $workflow[2]['this']['id']);
        $this->assertEmpty($workflow[2]['next']);
        $this->assertEquals('2', $workflow[2]['prev'][0]['id']);

        $this->assertEquals('4', $workflow[3]['this']['id']);
        $this->assertEmpty($workflow[2]['next']);
        $this->assertEquals('2', $workflow[3]['prev'][0]['id']);

    }

    public function testRetrieveEmpty()
    {

        $response = $this->call('POST', '/api/workflow', array(
            'action' => $this->retrieveAction,
        ));

        $this->assertEmpty($response->json());
    }
}
