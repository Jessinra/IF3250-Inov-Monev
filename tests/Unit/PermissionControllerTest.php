<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;


class PermissionControllerTest extends TestCase
{
    // use RefreshDatabase;

    private $createAction = "create";
    private $readAction = "read";
    private $updateAction = "update";
    private $deleteAction = "delete";

    private $testPermissionName = "test permission";
    private $testPermissionDesc = 'permission for test only';

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();          // show error stacktrace
        DB::table('permissions')->truncate();   // reset id increment
        $this->setOutputCallback(function () {
        }); // hide echos
    }

    /*===========================================
                    Routing section
     ===========================================*/

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

//    TODO: Test authorized

    /*===========================================
                Routing section
    ===========================================*/

    public function testCreate()
    {

        $this->call('POST', '/permission', array(
            'action' => $this->createAction,
            'name' => $this->testPermissionName,
            'description' => $this->testPermissionDesc
        ));

        $this->assertDatabaseHas('permissions', [
            'name' => $this->testPermissionName,
            'description' => $this->testPermissionDesc
        ]);
    }

    public function testCreateWithoutName()
    {
        /*
         *   Creating permission without name is not allowed
         */

        $this->call('POST', '/permission', array(
            'action' => $this->createAction,
            'description' => $this->testPermissionDesc
        ));

        $this->assertDatabaseMissing('permissions', [
            'name' => $this->testPermissionName,
            'description' => $this->testPermissionDesc
        ]);
    }

    public function testCreateWithoutDescription()
    {
        /*
         *   Creating permission without desc is not allowed
         */

        $this->call('POST', '/permission', array(
            'action' => $this->createAction,
            'name' => $this->testPermissionName,
        ));

        $this->assertDatabaseMissing('permissions', [
            'name' => $this->testPermissionName,
        ]);

    }

    public function testCreateEmptyName()
    {

        $this->call('POST', '/permission', array(
            'action' => $this->createAction,
            'name' => "",
        ));

        $this->assertDatabaseMissing('permissions', [
            'name' => "",
        ]);
    }

    public function testCreateEmptyDesc()
    {

        $this->call('POST', '/permission', array(
            'action' => $this->createAction,
            'name' => $this->testPermissionName,
            'description' => ""
        ));

        $this->assertDatabaseHas('permissions', [
            'name' => $this->testPermissionName,
            'description' => ""
        ]);
    }

    public function testCreateDuplicateEntry()
    {
        /*
         * Duplicate entry should be rejected
         */

        $this->call('POST', '/permission', array(
            'action' => $this->createAction,
            'name' => $this->testPermissionName,
            'description' => $this->testPermissionDesc
        ));

        $this->assertDatabaseHas('permissions', [
            'name' => $this->testPermissionName,
            'description' => $this->testPermissionDesc
        ]);

        $this->call('POST', '/permission', array(
            'action' => $this->createAction,
            'name' => $this->testPermissionName,
            'description' => "duplicate"
        ));

        $this->assertDatabaseMissing('permissions', [
            'name' => $this->testPermissionName,
            'description' => "duplicate"
        ]);

    }

    public function testCreateEntryTooLong()
    {

        /*
         * Entry string cap at 255 char
         */

        $this->call('POST', '/permission', array(
            'action' => $this->createAction,
            'name' => $this->testPermissionName,
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

        $this->assertDatabaseMissing('permissions', [
            'name' => $this->testPermissionName,
        ]);
    }


    private function createDummyPermission($copy = "")
    {

        $this->call('POST', '/permission', array(
            'action' => $this->createAction,
            'name' => $this->testPermissionName . $copy,
            'description' => $this->testPermissionDesc . $copy
        ));
    }

    /*===========================================
                Read section
     ===========================================*/

    public function testRead()
    {

        $this->createDummyPermission();

        $response = $this->call('POST', '/permission', array(
            'action' => $this->readAction,
            'id' => 1,
        ));

        $response->assertSee($this->testPermissionName);
    }

    public function testReadNotFound()
    {

        $this->createDummyPermission();

        $response = $this->call('POST', '/permission', array(
            'action' => $this->readAction,
            'id' => 2,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testPermissionName);
    }

    public function testReadWithoutId()
    {

        $this->createDummyPermission();

        $response = $this->call('POST', '/permission', array(
            'action' => $this->readAction,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testPermissionName);
    }

    /*===========================================
                Update section
    ===========================================*/

    public function testUpdate()
    {

        $this->createDummyPermission();

        $this->call('POST', '/permission', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc"
        ));

        $this->assertDatabaseHas('permissions', [
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

        $this->createDummyPermission();

        $this->call('POST', '/permission', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
        ));

        $this->assertDatabaseMissing('permissions', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('permissions', [
            'id' => 1,
            'name' => $this->testPermissionName,
            'description' => $this->testPermissionDesc
        ]);
    }

    public function testUpdateEmptyField()
    {
        /*
         * Empty field should not be accepted
         */

        $this->createDummyPermission();

        $this->call('POST', '/permission', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "",
            'description' => ""
        ));

        $this->assertDatabaseMissing('permissions', [
            'id' => 1,
            'name' => "",
            'description' => ""
        ]);

        $this->assertDatabaseHas('permissions', [
            'id' => 1,
            'name' => $this->testPermissionName,
            'description' => $this->testPermissionDesc
        ]);
    }

    public function testUpdateEntryTooLong()
    {

        $this->createDummyPermission();

        $this->call('POST', '/permission', array(
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

        $this->assertDatabaseMissing('permissions', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('permissions', [
            'id' => 1,
            'name' => $this->testPermissionName,
            'description' => $this->testPermissionDesc
        ]);
    }

    /*===========================================
                Delete section
    ===========================================*/

    public function testDelete()
    {

        $this->createDummyPermission();

        $this->assertDatabaseHas('permissions', [
            'id' => 1,
        ]);

        $this->call('POST', '/permission', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseMissing('permissions', [
            'id' => 1,
        ]);
    }

    public function testDeleteNotFound()
    {

        $this->createDummyPermission();

        $this->call('POST', '/permission', array(
            'action' => $this->deleteAction,
            'id' => 2,
        ));

        $this->assertDatabaseHas('permissions', [
            'id' => 1,
        ]);
    }

    public function testDeleteWithoutId()
    {

        $this->createDummyPermission();

        $this->call('POST', '/permission', array(
            'action' => $this->deleteAction,
        ));

        $this->assertDatabaseHas('permissions', [
            'id' => 1,
        ]);
    }

    /*===========================================
                Other section
    ===========================================*/

    public function testFetchAll()
    {

        $this->createDummyPermission(1);
        $this->createDummyPermission(2);
        $this->createDummyPermission(3);

        $this->assertDatabaseHas('permissions', [
            'id' => [1, 2, 3],
        ]);

        $this->assertDatabaseMissing('permissions', [
            'id' => 4,
        ]);

        $response = $this->call('POST', '/permission', array(
            'action' => "fetchAll",
        ));

        $response->assertSee($this->testPermissionName . "1");
        $response->assertSee($this->testPermissionName . "2");
        $response->assertSee($this->testPermissionName . "3");
    }
}
