<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StageControllerTest extends TestCase
{
    use RefreshDatabase;

    private $createAction = "create";
    private $retrieveAction = "retrieve";
    private $updateAction = "update";
    private $deleteAction = "delete";

    private $testStageName = "test stage";
    private $testStageDesc = 'stage for test only';
    private $testStageEditable = "false";
    private $testStageDeletable = "false";

    private $testPermissionCount = 2;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();          // show error stacktrace

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('project_stage')->truncate();
        DB::table('stages')->truncate();       // reset id increment

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->setOutputCallback(function () {      // hide echos
        });
    }

    /*===========================================
                    Routing section
     ===========================================*/

    public function testGETRoute()
    {

        $response = $this->get('/api/stage');

        $response->assertStatus(200);
//        $response->assertSee("Stage management page");
    }

    public function testPOSTRouteWithoutAction()
    {

        $response = $this->call('POST', '/api/stage', array());

        $response->assertStatus(200);
        $response->assertSee("abort 404");
    }

    public function testPOSTRouteWithAction()
    {

        $response = $this->call('POST', '/api/stage', array(
            'action' => 'test only',
        ));

        $response->assertStatus(200);
        $response->assertSee("Stage management page");
    }

//    TODO: Test authorized

    /*===========================================
                Create section
    ===========================================*/

    public function testCreate()
    {
        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc,
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseHas('stages', [
            'id' => 1,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc,
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ]);

    }

    public function testCreateWithoutName()
    {
        /*
         *   Creating stage without name is not allowed
         */

        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'description' => $this->testStageDesc,
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
        ]);
    }

    public function testCreateEmptyName()
    {

        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => "",
            'description' => $this->testStageDesc,
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
        ]);
    }

    public function testCreateWithoutDescription()
    {
        /*
         *   Creating stage without desc is not allowed
         */

        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
        ]);
    }

    public function testCreateEmptyDescription()
    {

        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'description' => "",
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseHas('stages', [
            'id' => 1,
        ]);
    }

    public function testCreateWithoutEditable()
    {
        /*
         *   Creating stage without desc is allowed
         */

        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseHas('stages', [
            'id' => 1,
            'editable' => "false"
        ]);
    }

    public function testCreateEmptyEditable()
    {

        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc,
            'editable' => "",
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'editable' => "",
        ]);
    }

    public function testCreateInvalidEditable()
    {

        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc,
            'editable' => "aaaa",
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'editable' => "aaaa",
        ]);
    }

    public function testCreateWithoutDeletable()
    {
        /*
         *   Creating stage without desc is not allowed
         */

        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc,
            'editable' => $this->testStageEditable,
        ));

        $this->assertDatabaseHas('stages', [
            'id' => 1,
            'deletable' => "false",
        ]);
    }

    public function testCreateEmptyDeletable()
    {
        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc,
            'editable' => $this->testStageEditable,
            'deletable' => "",
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'deletable' => "",
        ]);
    }

    public function testCreateInvalidDeletable()
    {
        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc,
            'editable' => $this->testStageEditable,
            'deletable' => "aaaa",
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'deletable' => "aaaa",
        ]);
    }

    public function testCreateEntryTooLong()
    {

        /*
         * Entry string cap at 255 char
         */

        $this->call('POST', '/api/stage', array(
            'action' => $this->createAction,
            'name' => $this->testStageName,
            'description' => "superduperlong;alskdjf;alskdjf;alskdjf;alskdfj;alsdkfj;las]
            ;laskdjf;alsdkfj;asldfkja;sldfkja;sdlfkajs;dflkajs;dflkajsdf;lkasjf;aksdjfasd
            ;aldfkja;lsdkfja;sldfkja;sdflkjas;dflkasjdf;lkasjdf;laskdjf;alkdjasdfasdfsadf
            ;alskdfj;alsdkfj;asldkfja;sldfkja;sldfkjas;ldfkjsa;dlkfjasl;fkjadasdfasdfsdfd
            ;alskdfj;alskdfj;aslkdfja;sldkfja;sdlfkjas;dflkjas;dlfkajsdf;lksajdf;kasdfsdf
            ;alskdfj;alskjdf;alsdkfj;asldkfj;alskfj;slkdfja;sldkfja;lsdkfj;asldkfj;asldkf
            ;alsdkfj;alsdkfj;asldkfj;asldkfj;asldkfj;asldkfj;aslkdfj;alskfj;alskdjf;alskd
            ;alskfdj;alskdfj;alskdfj;alskfdj;alskdfj;asldkfj;asdlkfja;slfkja;dslkfja;sldk
            ;alsdkfj;alsdkfj;alskdfj;alskdfj;alsdkfj;alskdfj;alskdfj;alskdjfasdfsadfasdfa
            ;alskdfj;alskdjf;alsdkfj;alskdfj;alskjf;alsdkfj;asldkfja;lsdkfja;lskfj;sldkaf
            ",
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
        ]);
    }


    /*===========================================
                Retrieve section
     ===========================================*/

    public function testRetrieve()
    {

        $this->createDummyStage();

        $response = $this->call('POST', '/api/stage', array(
            'action' => $this->retrieveAction,
            'id' => 1,
        ));

        $response->assertSee($this->testStageName);
    }

    public function testRetrieveNotFound()
    {

        $this->createDummyStage();

        $response = $this->call('POST', '/api/stage', array(
            'action' => $this->retrieveAction,
            'id' => 2,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testStageName);
    }

    public function testRetrieveWithoutId()
    {

        $this->createDummyStage();

        $response = $this->call('POST', '/api/stage', array(
            'action' => $this->retrieveAction,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testStageName);
    }

    public function testRetrieveActiveProject()
    {

    }

    public function testRetrieveNoActiveProject()
    {

    }

    public function testRetrieveVisitedProject()
    {

    }

    /*===========================================
                Update section
    ===========================================*/

    public function testUpdate()
    {

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc",
            'editable' => "true",
            'deletable' => "true",
        ));

        $this->assertDatabaseHas('stages', [
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc"
        ]);
    }

    public function testUpdateNonExisting()
    {

        $this->createDummyStage();

        $this->assertDatabaseMissing('stages', [
            'id' => 2,
        ]);

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 2,
            'name' => "updatedName",
            'description' => "updatedDesc",
            'editable' => "true",
            'deletable' => "true",
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 2,
        ]);

        $this->assertDatabaseHas('stages', [
            'id' => 1,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc
        ]);
    }

    public function testUpdateMissingField()
    {
        /*
         * Missing field should not be accepted (corrupt request)
         */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('stages', [
            'id' => 1,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc
        ]);
    }

    public function testUpdateEmptyField()
    {
        /*
         * Should not be accepted
         */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "",
            'description' => "",
            'editable' => "",
            'deletable' => "",
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'name' => "",
            'description' => ""
        ]);

        $this->assertDatabaseHas('stages', [
            'id' => 1,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc
        ]);
    }

    public function testUpdateEntryTooLong()
    {

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
            'description' => "superduperlong;alskdjf;alskdjf;alskdjf;alskdfj;alsdkfj;las]
            ;laskdjf;alsdkfj;asldfkja;sldfkja;sdlfkajs;dflkajs;dflkajsdf;lkasjf;aksdjfasd
            ;aldfkja;lsdkfja;sldfkja;sdflkjas;dflkasjdf;lkasjdf;laskdjf;alkdjasdfasdfsadf
            ;alskdfj;alsdkfj;asldkfja;sldfkja;sldfkjas;ldfkjsa;dlkfjasl;fkjadasdfasdfsdfd
            ;alskdfj;alskdfj;aslkdfja;sldkfja;sdlfkjas;dflkjas;dlfkajsdf;lksajdf;kasdfsdf
            ;alskdfj;alskjdf;alsdkfj;asldkfj;alskfj;slkdfja;sldkfja;lsdkfj;asldkfj;asldkf
            ;alsdkfj;alsdkfj;asldkfj;asldkfj;asldkfj;asldkfj;aslkdfj;alskfj;alskdjf;alskd
            ;alskfdj;alskdfj;alskdfj;alskfdj;alskdfj;asldkfj;asdlkfja;slfkja;dslkfja;sldk
            ;alsdkfj;alsdkfj;alskdfj;alskdfj;alsdkfj;alskdfj;alskdfj;alskdjfasdfsadfasdfa
            ;alskdfj;alskdjf;alsdkfj;alskdfj;alskjf;alsdkfj;asldkfja;lsdkfja;lskfj;sldkaf
            "
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('stages', [
            'id' => 1,
            'name' => $this->testStageName,
            'description' => $this->testStageDesc
        ]);
    }


    /*===========================================*/

    public function testUpdateWithoutName()
    {
        /*
         *   Creating stage without name is not allowed
         */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'description' => "updated",
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'description' => "updated"
        ]);
    }

    public function testUpdateEmptyName()
    {
        /*
        *   Creating stage empty name is not allowed
        */

        $this->createDummyStage();
        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "",
            'description' => $this->testStageDesc,
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'name' => "",
        ]);
    }

    public function testUpdateWithoutDescription()
    {
        /*
         *   Creating stage without description is not allowed
         */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updated",
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'name' => "updated"
        ]);
    }

    public function testUpdateEmptyDescription()
    {
        /*
        *   Creating stage empty description is not allowed
        */
        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testStageName,
            'description' => "",
            'editable' => $this->testStageEditable,
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'description' => "",
        ]);
    }

    public function testUpdateWithoutEditable()
    {
        /*
         *   Creating stage without editable is not allowed
         */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testStageName,
            'description' => "updated",
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'description' => "updated",
        ]);
    }

    public function testUpdateEmptyEditable()
    {
        /*
        *   Creating stage empty editable is not allowed
        */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testStageName,
            'description' => "updated",
            'editable' => "",
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'description' => "updated",
        ]);
    }

    public function testUpdateInvalidEditable()
    {
        /*
        *   Creating stage empty editable is not allowed
        */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testStageName,
            'description' => "updated",
            'editable' => "aaaa",
            'deletable' => $this->testStageDeletable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'description' => "updated",
        ]);
    }

    public function testUpdateWithoutDeletable()
    {
        /*
         *   Creating stage without deletable is not allowed
         */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testStageName,
            'description' => "updated",
            'editable' => $this->testStageEditable,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'description' => "updated"
        ]);
    }

    public function testUpdateEmptyDeletable()
    {
        /*
        *   Creating stage empty deletable is not allowed
        */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testStageName,
            'description' => "updated",
            'editable' => $this->testStageEditable,
            'deletable' => "",
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'description' => "updated",
        ]);
    }

    public function testUpdateInvalidDeletable()
    {
        /*
        *   Creating stage empty deletable is not allowed
        */

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testStageName,
            'description' => "updated",
            'editable' => $this->testStageEditable,
            'deletable' => "aaaa",
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
            'description' => "updated",
        ]);
    }


    /*===========================================
                Delete section
    ===========================================*/

    public function testDelete()
    {

        $this->createDummyStage();

        $this->assertDatabaseHas('stages', [
            'id' => 1,
        ]);

        $this->call('POST', '/api/stage', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseMissing('stages', [
            'id' => 1,
        ]);
    }

    public function testDeleteNotFound()
    {

        $this->createDummyStage();

        $this->assertDatabaseHas('stages', [
            'id' => 1,
        ]);

        $this->call('POST', '/api/stage', array(
            'action' => $this->deleteAction,
            'id' => 2,
        ));

        $this->assertDatabaseHas('stages', [
            'id' => 1,
        ]);
    }

    public function testDeleteWithoutId()
    {

        $this->createDummyStage();

        $this->call('POST', '/api/stage', array(
            'action' => $this->deleteAction,
        ));

        $this->assertDatabaseHas('stages', [
            'id' => 1,
        ]);

    }

    public function testDeleteWithPrevStages()
    {
        // not ok
    }

    public function testDeleteWithNextStages()
    {
        // ok
    }

    /*===========================================
             Integration with Project
    ===========================================*/

    public function testAddProject()
    {

    }

    public function testRemoveProject()
    {

    }

    public function testDeleteWithActiveProject()
    {
        // not ok
    }

    public function testDeleteWithInactiveProjects()
    {
        // ok
    }

    /*===========================================
                Other section
    ===========================================*/

    public function testFetchAll()
    {

        $this->createDummyStage(1);
        $this->createDummyStage(2);
        $this->createDummyStage(3);

        $this->assertDatabaseHas('stages', [
            'id' => [1, 2, 3],
        ]);

        $this->assertDatabaseMissing('stages', [
            'id' => 4,
        ]);

        $response = $this->call('POST', '/api/stage', array(
            'action' => "fetchAll",
        ));

        $response->assertSee($this->testStageName . "1");
        $response->assertSee($this->testStageName . "2");
        $response->assertSee($this->testStageName . "3");
    }


}
