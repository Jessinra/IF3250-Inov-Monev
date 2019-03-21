<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    private $createAction = "create";
    private $readAction = "read";
    private $updateAction = "update";
    private $deleteAction = "delete";

    private $testGroupName = "test group";
    private $testGroupDesc = 'group for test only';

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();          // show error stacktrace
        DB::table('groups')->truncate();   // reset id increment
        $this->setOutputCallback(function () {
        }); // hide echos
    }

    /*===========================================
                    Routing section
     ===========================================*/

    public function testGETRoute()
    {

        $response = $this->get('/group');

        $response->assertStatus(200);
        $response->assertSee("Group management page");
    }

    public function testPOSTRouteWithoutAction()
    {

        $response = $this->call('POST', '/group', array());

        $response->assertStatus(200);
        $response->assertSee("abort 404");
    }

    public function testPOSTRouteWithAction()
    {

        $response = $this->call('POST', '/group', array(
            'action' => 'test only',
        ));

        $response->assertStatus(200);
        $response->assertSee("Group management page");
    }

//    TODO: Test authorized

    /*===========================================
                Routing section
    ===========================================*/

    public function testCreate()
    {

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'name' => $this->testGroupName,
            'description' => $this->testGroupDesc
        ));

        $this->assertDatabaseHas('groups', [
            'name' => $this->testGroupName,
            'description' => $this->testGroupDesc
        ]);
    }

    public function testCreateWithoutName()
    {
        /*
         *   Creating group without name is not allowed
         */

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'description' => $this->testGroupDesc
        ));

        $this->assertDatabaseMissing('groups', [
            'name' => $this->testGroupName,
            'description' => $this->testGroupDesc
        ]);
    }

    public function testCreateWithoutDescription()
    {
        /*
         *   Creating group without desc is not allowed
         */

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'name' => $this->testGroupName,
        ));

        $this->assertDatabaseMissing('groups', [
            'name' => $this->testGroupName,
        ]);

    }

    public function testCreateEmptyName()
    {

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'name' => "",
        ));

        $this->assertDatabaseMissing('groups', [
            'name' => "",
        ]);
    }

    public function testCreateEmptyDesc()
    {

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'name' => $this->testGroupName,
            'description' => ""
        ));

        $this->assertDatabaseHas('groups', [
            'name' => $this->testGroupName,
            'description' => ""
        ]);
    }

    public function testCreateDuplicateEntry()
    {
        /*
         * Duplicate entry should be rejected
         */

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'name' => $this->testGroupName,
            'description' => $this->testGroupDesc
        ));

        $this->assertDatabaseHas('groups', [
            'name' => $this->testGroupName,
            'description' => $this->testGroupDesc
        ]);

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'name' => $this->testGroupName,
            'description' => "duplicate"
        ));

        $this->assertDatabaseMissing('groups', [
            'name' => $this->testGroupName,
            'description' => "duplicate"
        ]);

    }

    public function testCreateEntryTooLong()
    {

        /*
         * Entry string cap at 255 char
         */

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'name' => $this->testGroupName,
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

        $this->assertDatabaseMissing('groups', [
            'name' => $this->testGroupName,
        ]);
    }


    private function createDummyGroup($copy = "")
    {

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'name' => $this->testGroupName . $copy,
            'description' => $this->testGroupDesc . $copy
        ));
    }

    /*===========================================
                Read section
     ===========================================*/

    public function testRead()
    {

        $this->createDummyGroup();

        $response = $this->call('POST', '/group', array(
            'action' => $this->readAction,
            'id' => 1,
        ));

        $response->assertSee($this->testGroupName);
    }

    public function testReadNotFound()
    {

        $this->createDummyGroup();

        $response = $this->call('POST', '/group', array(
            'action' => $this->readAction,
            'id' => 2,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testGroupName);
    }

    public function testReadWithoutId()
    {

        $this->createDummyGroup();

        $response = $this->call('POST', '/group', array(
            'action' => $this->readAction,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testGroupName);
    }

    /*===========================================
                Update section
    ===========================================*/

    public function testUpdate()
    {

        $this->createDummyGroup();

        $this->call('POST', '/group', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc"
        ));

        $this->assertDatabaseHas('groups', [
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc"
        ]);
    }

    public function testUpdateMissingField()
    {
        /*
         * Missing field should not be accepted (corrupt request)
         */

        $this->createDummyGroup();

        $this->call('POST', '/group', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
        ));

        $this->assertDatabaseMissing('groups', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('groups', [
            'id' => 1,
            'name' => $this->testGroupName,
            'description' => $this->testGroupDesc
        ]);
    }

    public function testUpdateEmptyField()
    {
        /*
         * Empty field should not be accepted
         */

        $this->createDummyGroup();

        $this->call('POST', '/group', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "",
            'description' => ""
        ));

        $this->assertDatabaseMissing('groups', [
            'id' => 1,
            'name' => "",
            'description' => ""
        ]);

        $this->assertDatabaseHas('groups', [
            'id' => 1,
            'name' => $this->testGroupName,
            'description' => $this->testGroupDesc
        ]);
    }

    public function testUpdateEntryTooLong()
    {

        $this->createDummyGroup();

        $this->call('POST', '/group', array(
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

        $this->assertDatabaseMissing('groups', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('groups', [
            'id' => 1,
            'name' => $this->testGroupName,
            'description' => $this->testGroupDesc
        ]);
    }

    /*===========================================
                Delete section
    ===========================================*/

    public function testDelete()
    {

        $this->createDummyGroup();

        $this->assertDatabaseHas('groups', [
            'id' => 1,
        ]);

        $this->call('POST', '/group', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseMissing('groups', [
            'id' => 1,
        ]);
    }

    public function testDeleteNotFound()
    {

        $this->createDummyGroup();

        $this->call('POST', '/group', array(
            'action' => $this->deleteAction,
            'id' => 2,
        ));

        $this->assertDatabaseHas('groups', [
            'id' => 1,
        ]);
    }

    public function testDeleteWithoutId()
    {

        $this->createDummyGroup();

        $this->call('POST', '/group', array(
            'action' => $this->deleteAction,
        ));

        $this->assertDatabaseHas('groups', [
            'id' => 1,
        ]);
    }

    /*===========================================
                Other section
    ===========================================*/

    public function testFetchAll()
    {

        $this->createDummyGroup(1);
        $this->createDummyGroup(2);
        $this->createDummyGroup(3);

        $this->assertDatabaseHas('groups', [
            'id' => [1, 2, 3],
        ]);

        $this->assertDatabaseMissing('groups', [
            'id' => 4,
        ]);

        $response = $this->call('POST', '/group', array(
            'action' => "fetchAll",
        ));

        $response->assertSee($this->testGroupName . "1");
        $response->assertSee($this->testGroupName . "2");
        $response->assertSee($this->testGroupName . "3");
    }
}
