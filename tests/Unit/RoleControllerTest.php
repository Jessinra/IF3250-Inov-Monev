<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{

    use RefreshDatabase;

    private $createAction = "create";
    private $readAction = "read";
    private $updateAction = "update";
    private $deleteAction = "delete";

    private $testRoleName = "test role";
    private $testRoleDesc = 'role for test only';
    private $testPermissionCount = 2;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();          // show error stacktrace

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permission')->truncate();       // reset id increment
        DB::table('roles')->truncate();       // reset id increment
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->setOutputCallback(function () {      // hide echos
        });
    }

    /*===========================================
                    Routing section
     ===========================================*/

    public function testGETRoute()
    {

        $response = $this->get('/api/role');

        $response->assertStatus(200);
//        $response->assertSee("Role management page");
    }

    public function testPOSTRouteWithoutAction()
    {

        $response = $this->call('POST', '/api/role', array());

        $response->assertStatus(200);
        $response->assertSee("abort 404");
    }

    public function testPOSTRouteWithAction()
    {

        $response = $this->call('POST', '/api/role', array(
            'action' => 'test only',
        ));

        $response->assertStatus(200);
        $response->assertSee("Role management page");
    }

//    TODO: Test authorized

    /*===========================================
                Create section
    ===========================================*/

    public function testCreate()
    {

        $this->createDummyPermission("1");
        $this->createDummyPermission("2");

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc,
            'permissionCount' => $this->testPermissionCount,
            'permission0' => 1,
            'permission1' => 2
        ));

        $this->assertDatabaseHas('roles', [
            'id' => 1,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);

        $this->assertDatabaseHas('role_permission', [
            'role_id' => 1,
            'permission_id' => [1, 2]
        ]);
    }

    public function testCreateWithoutName()
    {
        /*
         *   Creating role without name is not allowed
         */

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'description' => $this->testRoleDesc
        ));

        $this->assertDatabaseMissing('roles', [
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);
    }

    public function testCreateEmptyName()
    {

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => "",
        ));

        $this->assertDatabaseMissing('roles', [
            'name' => "",
        ]);
    }

    public function testCreateWithoutDescription()
    {
        /*
         *   Creating role without desc is not allowed
         */

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName,
        ));

        $this->assertDatabaseMissing('roles', [
            'name' => $this->testRoleName,
        ]);

    }

    public function testCreateEmptyDesc()
    {

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName,
            'description' => ""
        ));

        $this->assertDatabaseHas('roles', [
            'name' => $this->testRoleName,
            'description' => ""
        ]);
    }

    public function testCreateWithoutPermission()
    {

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc,
            'permissionCount' => $this->testPermissionCount,
        ));

        $this->assertDatabaseHas('roles', [
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);

        $this->assertDatabaseMissing('role_permission', [
            'role_id' => 1,
            'permission_id' => [1, 2]
        ]);
    }

    public function testCreateEmptyPermission()
    {

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc,
            'permissionCount' => $this->testPermissionCount,
            'permission0' => ""
        ));

        $this->assertDatabaseHas('roles', [
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);

        $this->assertDatabaseMissing('role_permission', [
            'role_id' => 1,
            'permission_id' => [1, 2]
        ]);
    }

    public function testCreateNonExistPermission()
    {

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc,
            'permissionCount' => $this->testPermissionCount,
            'permission0' => 101,
            'permission1' => 102
        ));

        $this->assertDatabaseHas('roles', [
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);

        $this->assertDatabaseMissing('role_permission', [
            'role_id' => 1,
            'permission_id' => [101, 102]
        ]);
    }

    public function testCreateDuplicateEntry()
    {
        /*
         * Duplicate entry should be rejected
         */

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ));

        $this->assertDatabaseHas('roles', [
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName,
            'description' => "duplicate"
        ));

        $this->assertDatabaseMissing('roles', [
            'name' => $this->testRoleName,
            'description' => "duplicate"
        ]);

    }

    public function testCreateEntryTooLong()
    {

        /*
         * Entry string cap at 255 char
         */

        $this->call('POST', '/api/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName,
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

        $this->assertDatabaseMissing('roles', [
            'name' => $this->testRoleName,
        ]);
    }


    /*===========================================
                Read section
     ===========================================*/

    public function testRead()
    {

        $this->createDummyRole();

        $response = $this->call('POST', '/api/role', array(
            'action' => $this->readAction,
            'id' => 1,
        ));

        $response->assertSee($this->testRoleName);
    }

    public function testReadNotFound()
    {

        $this->createDummyRole();

        $response = $this->call('POST', '/api/role', array(
            'action' => $this->readAction,
            'id' => 2,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testRoleName);
    }

    public function testReadWithoutId()
    {

        $this->createDummyRole();

        $response = $this->call('POST', '/api/role', array(
            'action' => $this->readAction,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testRoleName);
    }

    /*===========================================
                Update section
    ===========================================*/

    public function testUpdate()
    {

        $this->createDummyRole();

        $this->call('POST', '/api/role', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc",
            'permissionCount' => $this->testPermissionCount,
            'permission0' => 1,
        ));

        $this->assertDatabaseHas('roles', [
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc"
        ]);

        $this->assertDatabaseHas('role_permission', [
            'role_id' => 1,
            'permission_id' => [1]
        ]);

        $this->assertDatabaseMissing('role_permission', [
            'role_id' => 1,
            'permission_id' => [2]
        ]);
    }

    public function testUpdateMissingField()
    {
        /*
         * Missing field should not be accepted (corrupt request)
         */

        $this->createDummyRole();

        $this->call('POST', '/api/role', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
        ));

        $this->assertDatabaseMissing('roles', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => 1,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);
    }

    public function testUpdateEmptyField()
    {
        /*
         * Should not be accepted
         */

        $this->createDummyRole();

        $this->call('POST', '/api/role', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "",
            'description' => "",
            'permissionCount' => "",
            'permission0' => "",
        ));

        $this->assertDatabaseMissing('roles', [
            'id' => 1,
            'name' => "",
            'description' => ""
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => 1,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);
    }

    public function testUpdateEntryTooLong()
    {

        $this->createDummyRole();

        $this->call('POST', '/api/role', array(
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

        $this->assertDatabaseMissing('roles', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => 1,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);
    }

    public function testUpdateNonExistPermission()
    {
        /*
         * Should not be accepted, the name description should be changed, but not the permission
         */

        $this->createDummyRole();

        $this->call('POST', '/api/role', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc",
            'permissionCount' => 3,
            'permission0' => 1,
            'permission1' => 2,
            'permission2' => 103,   // non exist
        ));

        $this->assertDatabaseMissing('roles', [
            'id' => 1,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc,
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc",
        ]);

        $this->assertDatabaseMissing('role_permission', [
            'role_id' => 1,
            'permission_id' => [103]
        ]);

        $this->assertDatabaseHas('role_permission', [
            'role_id' => 1,
            'permission_id' => [1, 2]
        ]);


    }

    public function testUpdateEmptyPermission()
    {
        /*
         * Should be be accepted (revoke all access)
         */

        $this->createDummyRole();

        $this->call('POST', '/api/role', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc",
            'permissionCount' => $this->testPermissionCount,
            'permission0' => "",
        ));

        $this->assertDatabaseMissing('roles', [
            'id' => 1,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc,
        ]);

        $this->assertDatabaseMissing('role_permission', [
            'role_id' => 1,
            'permission_id' => [1, 2]
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => 1,
            'name' => "updatedName",
            'description' => "updatedDesc",
        ]);
    }

    /*===========================================
                Delete section
    ===========================================*/

    public function testDelete()
    {

        $this->createDummyRole();

        $this->assertDatabaseHas('roles', [
            'id' => 1,
        ]);

        $this->call('POST', '/api/role', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseMissing('roles', [
            'id' => 1,
        ]);

        $this->assertDatabaseMissing('role_permission', [
            'role_id' => 1,
            'permission_id' => [1, 2]
        ]);
    }

    public function testDeleteNotFound()
    {

        $this->createDummyRole();

        $this->call('POST', '/api/role', array(
            'action' => $this->deleteAction,
            'id' => 2,
        ));

        $this->assertDatabaseHas('roles', [
            'id' => 1,
        ]);

        $this->assertDatabaseHas('role_permission', [
            'role_id' => 1,
            'permission_id' => [1, 2]
        ]);
    }

    public function testDeleteWithoutId()
    {

        $this->createDummyRole();

        $this->call('POST', '/api/role', array(
            'action' => $this->deleteAction,
        ));

        $this->assertDatabaseHas('roles', [
            'id' => 1,
        ]);

        $this->assertDatabaseHas('role_permission', [
            'role_id' => 1,
            'permission_id' => [1, 2]
        ]);
    }

    public function testDeleteWontEffectOtherRoleWithSamePermission()
    {
        /*
         * Delete should not interfere with other role with same permission
         */

        $this->createDummyRole("1");
        $this->createDummyRole("2");

        $this->call('POST', '/api/role', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseMissing('roles', [
            'id' => 1,
            'name' => $this->testRoleName . "1",
            'description' => $this->testRoleDesc . "1"
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => 2,
            'name' => $this->testRoleName . "2",
            'description' => $this->testRoleDesc . "2"
        ]);

        $this->assertDatabaseMissing('role_permission', [
            'role_id' => 1,
            'permission_id' => 1,
        ]);

        $this->assertDatabaseHas('role_permission', [
            'role_id' => 2,
            'permission_id' => 1,
        ]);
    }

    /*===========================================
             Permission Deleted section
    ===========================================*/

    public function testPermissionOwnGotDeleted()
    {
        $this->createDummyRole();

        // Delete the permission
        $this->call('POST', '/api/permissions', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseHas('roles', [
            'id' => 1,
            'name' => $this->testRoleName,
            'description' => $this->testRoleDesc
        ]);

        $this->assertDatabaseMissing('role_permission', [
            'role_id' => 1,
            'permission_id' => 1.
        ]);

        $this->assertDatabaseHas('role_permission', [
            'role_id' => 1,
            'permission_id' => 2,
        ]);
    }

    /*===========================================
                Other section
    ===========================================*/

    public function testFetchAll()
    {

        $this->createDummyRole(1);
        $this->createDummyRole(2);
        $this->createDummyRole(3);

        $this->assertDatabaseHas('roles', [
            'id' => [1, 2, 3],
        ]);

        $this->assertDatabaseMissing('roles', [
            'id' => 4,
        ]);

        $response = $this->call('POST', '/api/role', array(
            'action' => "fetchAll",
        ));

        $response->assertSee($this->testRoleName . "1");
        $response->assertSee($this->testRoleName . "2");
        $response->assertSee($this->testRoleName . "3");
    }
}
