<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private $SILENT = true;

    private $createAction = "create";
    private $readAction = "read";
    private $updateAction = "update";
    private $deleteAction = "delete";

    private $testName = "test user";
    private $testUsername = 'test username';
    private $testEmail = "testemail@email.com";
    private $testPassword = "test pass";

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();          // show error stacktrace

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('user_role')->truncate();       // reset id increment
        DB::table('user_group')->truncate();       // reset id increment
        DB::table('users')->truncate();       // reset id increment
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        if ($this->SILENT) {
            $this->setOutputCallback(function () {      // hide echos
            });
        }
    }

    /*===========================================
                    Routing section
     ===========================================*/

    public function testGETRoute()
    {

        $response = $this->get('/user');

        $response->assertStatus(200);
//        $response->assertSee("User management page");
    }

    public function testPOSTRouteWithoutAction()
    {

        $response = $this->call('POST', '/api/user', array());

        $response->assertStatus(200);
        $response->assertSee("abort 404");
    }

    public function testPOSTRouteWithAction()
    {

        $response = $this->call('POST', '/api/user', array(
            'action' => 'test only',
        ));

        $response->assertStatus(200);
        $response->assertSee("User management page");
    }

//    TODO: Test authorized

    /*===========================================
                Create section
    ===========================================*/

    public function testCreateUserOnly()
    {
        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);
    }

    public function testCreateWrongPasswordConfirmation()
    {
        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => "",
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);
    }

    public function testCreateWithRole()
    {
        $this->createDummyRole();

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => 1,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_role', [
            "user_id" => 1,
            "role_id" => 1
        ]);
    }

    public function testCreateWithGroup()
    {
        $this->createDummyGroup();

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => 1,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_group', [
            "user_id" => 1,
            "group_id" => 1
        ]);
    }

    public function testCreateWithoutName()
    {
        /*
         *   Creating user without name is not allowed
         */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'username' => $this->testUsername
        ]);
    }

    public function testCreateEmptyName()
    {
        /*
        *   Creating user empty name is not allowed
        */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => "",
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "",
        ]);
    }

    public function testCreateWithoutUsername()
    {
        /*
         *   Creating user without username is not allowed
         */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'username' => $this->testName,
        ]);

    }

    public function testCreateEmptyUsername()
    {

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => "",
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'username' => ""
        ]);
    }

    public function testCreateWithoutEmail()
    {

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
        ]);
    }

    public function testCreateEmptyEmail()
    {

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => "",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => "",
        ]);
    }

    public function testCreateWithoutPassword()
    {

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testPassword,
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testPassword,
        ]);
    }

    public function testCreateEmptyPassword()
    {

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testPassword,
            'password' => "",
            'password_confirmation' => ""
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testPassword,
        ]);
    }

    public function testCreateNonExistRole()
    {
        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => 100,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseMissing('user_role', [
            "user_id" => 1,
            "role_id" => 100,
        ]);
    }

    public function testCreateEmptyRole()
    {
        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => "",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseMissing('user_role', [
            "user_id" => 1,
        ]);
    }

    public function testCreateNonExistGroup()
    {
        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => 100,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseMissing('user_group', [
            "user_id" => 1,
            "group_id" => 100,
        ]);
    }

    public function testCreateEmptyGroup()
    {
        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => "",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseMissing('user_group', [
            "user_id" => 1,
        ]);
    }

    public function testCreateDuplicateEntry()
    {
        /*
         * Duplicate entry should be rejected
         */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 2,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);
    }

    public function testCreateDuplicateName()
    {
        /*
         *  Its ok :)
         */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 2,
            'name' => $this->testName,
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
        ]);
    }

    public function testCreateEntryTooLong()
    {

        /*
         * Entry string cap at 255 char
         */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => "superduperlong;alskdjf;alskdjf;alskdjf;alskdfj;alsdkfj;las]
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
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'name' => $this->testName,
        ]);
    }


    /*===========================================
                Read section
     ===========================================*/

    public function testRead()
    {

        $this->createDummyUser();

        $response = $this->call('POST', '/api/user', array(
            'action' => $this->readAction,
            'id' => 1,
        ));

        $response->assertSee($this->testName);
    }

    public function testReadNotFound()
    {

        $this->createDummyUser();

        $response = $this->call('POST', '/api/user', array(
            'action' => $this->readAction,
            'id' => 2,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testName);
    }

    public function testReadWithoutId()
    {

        $this->createDummyUser();

        $response = $this->call('POST', '/api/user', array(
            'action' => $this->readAction,
        ));

        $response->assertSee('[]');
        $response->assertDontSee($this->testName);
    }

    /*===========================================
                Update section
    ===========================================*/

    public function testUpdate()
    {

        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
            'username' => "updatedDesc",
            'email' => "updatedEmail@email.com",
            'password' => "updated password",
            'password_confirmation' => "updated password"
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => "updatedName",
            'username' => "updatedDesc",
            'email' => "updatedEmail@email.com",
        ]);
    }

    public function testUpdateNonExisting()
    {

        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 2,
            'name' => "updatedName",
            'username' => "updatedDesc",
            'email' => "updatedEmail@email.com",
            'password' => "updated password",
            'password_confirmation' => "updated password"
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 2,
            'name' => "updatedName",
            'username' => "updatedDesc",
            'email' => "updatedEmail@email.com",
        ]);
    }

    public function testUpdateDoNothing()
    {
        /*
         *  Send update, but changes nothing : OK
         */

        $this->createDummyUser();
        $this->assertDatabaseHas('users', [
            'id' => 1,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);
    }

    public function testUpdateMissingField()
    {
        /*
         * Missing field should not be accepted (corrupt request)
         */

        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername
        ]);
    }

    public function testUpdateEmptyField()
    {
        /*
         * Should not be accepted
         */

        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "",
            'username' => "",
            'email' => "",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "",
            'username' => "",
            'email' => "",
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername
        ]);
    }

    public function testUpdateEntryTooLong()
    {

        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updatedName",
            'username' => "superduperlong;alskdjf;alskdjf;alskdjf;alskdfj;alsdkfj;las]
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
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "updatedName",
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);
    }

    /*===========================================*/

    public function testUpdateWithoutName()
    {
        /*
         *   Creating user without name is not allowed
         */
        $this->createDummyUser();
        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'username' => "updated",
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'username' => "updated"
        ]);
    }

    public function testUpdateEmptyName()
    {
        /*
        *   Creating user empty name is not allowed
        */
        $this->createDummyUser();
        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "",
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "",
        ]);
    }

    /*===========================================*/

    public function testUpdateWithoutUsername()
    {
        /*
         *   Creating user without username is not allowed
         */
        $this->createDummyUser();
        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updated",
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "updated",
        ]);

    }

    public function testUpdateEmptyUsername()
    {
        $this->createDummyUser();
        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updated",
            'username' => "",
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "updated",
        ]);
    }

    /*===========================================*/

    public function testUpdateWithoutEmail()
    {
        $this->createDummyUser();
        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updated",
            'username' => $this->testUsername,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "updated",
        ]);
    }

    public function testUpdateEmptyEmail()
    {
        $this->createDummyUser();
        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updated",
            'username' => $this->testUsername,
            'email' => "",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "updated",

        ]);
    }

    /*===========================================*/

    public function testUpdateWithoutPassword()
    {
        $this->createDummyUser();
        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updated",
            'username' => $this->testUsername,
            'email' => $this->testPassword,
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "updated",
        ]);
    }

    public function testUpdateEmptyPassword()
    {
        $this->createDummyUser();
        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => "updated",
            'username' => $this->testUsername,
            'email' => $this->testPassword,
            'password' => "",
            'password_confirmation' => ""
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => "updated",

        ]);
    }

    /*===========================================*/

    public function testUpdateNonExistRole()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => 100,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseMissing('user_role', [
            "user_id" => 1,
            "role_id" => 100,
        ]);
    }

    public function testUpdateEmptyRole()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => "",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_role', [
            "user_id" => 1,
            "role_id" => 1,
        ]);

        $this->assertDatabaseMissing('user_role', [
            "user_id" => 1,
            "role_id" => "",
        ]);
    }

    /*===========================================*/

    public function testUpdateNonExistGroup()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => 100,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_group', [
            "user_id" => 1,
            "group_id" => 1,
        ]);

        $this->assertDatabaseMissing('user_group', [
            "user_id" => 1,
            "group_id" => 100,
        ]);
    }

    public function testUpdateEmptyGroup()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => "",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_group', [
            "user_id" => 1,
            "group_id" => 1,
        ]);

        $this->assertDatabaseMissing('user_group', [
            "user_id" => 1,
            "group_id" => "",
        ]);
    }

    /*===========================================*/

    public function testUpdateAddRole()
    {
        $this->createDummyUser();
        $this->createDummyRole("1");
        $this->createDummyRole("2");

        $this->assertDatabaseHas('roles', [
            'id' => 2,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => 2,
            'addRole' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_role', [
            "user_id" => 1,
            "role_id" => 2,
        ]);
    }

    public function testUpdateRemoveRole()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => 1,
            'removeRole' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseMissing('user_role', [
            "user_id" => 1,
            "role_id" => 1,
        ]);
    }

    public function testUpdateAddGroup()
    {
        $this->createDummyUser();
        $this->createDummyGroup("1");
        $this->createDummyGroup("2");

        $this->assertDatabaseHas('groups', [
            'id' => 2,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => 2,
            'addGroup' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_group', [
            "user_id" => 1,
            "group_id" => 2,
        ]);
    }

    public function testUpdateRemoveGroup()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => 1,
            'removeGroup' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseMissing('user_group', [
            "user_id" => 1,
            "group_id" => 1,
        ]);
    }

    /*===========================================*/

    public function testUpdateAddNonExistRole()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => 100,
            'addRole' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseMissing('user_role', [
            "user_id" => 1,
            "role_id" => 100,
        ]);
    }

    public function testUpdateAddEmptyRole()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => "",
            'addRole' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_role', [
            "user_id" => 1,
            "role_id" => 1,
        ]);

        $this->assertDatabaseMissing('user_role', [
            "user_id" => 1,
            "role_id" => "",
        ]);
    }

    public function testUpdateAddNonExistGroup()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => 100,
            'addGroup' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_group', [
            "user_id" => 1,
            "group_id" => 1,
        ]);

        $this->assertDatabaseMissing('user_group', [
            "user_id" => 1,
            "group_id" => 100,
        ]);
    }

    public function testUpdateAddEmptyGroup()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => "",
            'addGroup' => "true",

        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_group', [
            "user_id" => 1,
            "group_id" => 1,
        ]);

        $this->assertDatabaseMissing('user_group', [
            "user_id" => 1,
            "group_id" => "",
        ]);
    }

    /*===========================================*/

    public function testUpdateRemoveNonExistRole()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => 100,
            'removeRole' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseMissing('user_role', [
            "user_id" => 1,
            "role_id" => 100,
        ]);
    }

    public function testUpdateRemoveEmptyRole()
    {
        $this->createDummyUser();

        $this->assertDatabaseHas('user_role', [
            "user_id" => 1,
            "role_id" => 1,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => "",
            'removeRole' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_role', [
            "user_id" => 1,
            "role_id" => 1,
        ]);

        $this->assertDatabaseMissing('user_role', [
            "user_id" => 1,
            "role_id" => "",
        ]);
    }

    public function testUpdateRemoveNonExistGroup()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => 100,
            'removeGroup' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_group', [
            "user_id" => 1,
            "group_id" => 1,
        ]);

        $this->assertDatabaseMissing('user_group', [
            "user_id" => 1,
            "group_id" => 100,
        ]);
    }

    public function testUpdateRemoveEmptyGroup()
    {
        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'groupId' => "",
            'removeGroup' => "true",
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->assertDatabaseHas('user_group', [
            "user_id" => 1,
            "group_id" => 1,
        ]);

        $this->assertDatabaseMissing('user_group', [
            "user_id" => 1,
            "group_id" => "",
        ]);
    }

    /*===========================================*/

    public function testUpdateDuplicateEntry()
    {
        /*
         * Duplicate entry should be rejected
         */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 2,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
        ]);

        // At this point, 2 user created with different name, lets crash them together

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
        ]);
    }

    public function testUpdateDuplicateName()
    {
        /*
         *  Its ok :)
         */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 2,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
        ]);

        // At this point, 2 user created with different name, lets crash them together

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName . "2",
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName . "2",
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);
    }

    public function testUpdateDuplicateUsername()
    {
        /*
         *  Its not ok
         */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 2,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
        ]);

        // At this point, 2 user created with different username, lets crash them together

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail,
        ]);
    }

    public function testUpdateDuplicateEmail()
    {
        /*
         *  Its not ok
         */

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->createAction,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseHas('users', [
            'id' => 2,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2",
            'email' => $this->testEmail . "2",
        ]);

        // At this point, 2 user created with different name, lets crash them together

        $this->call('POST', '/api/user', array(
            'action' => $this->updateAction,
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail . "2",
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername,
            'email' => $this->testEmail . "2",
        ]);
    }


    /*===========================================
                Delete section
    ===========================================*/

    public function testDelete()
    {

        $this->createDummyUser();

        $this->assertDatabaseHas('users', [
            'id' => 1,
        ]);

        $this->call('POST', '/api/user', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
        ]);

        $this->assertDatabaseMissing('user_role', [
            'user_id' => 1,
            'role_id' => [1, 2]
        ]);
    }

    public function testDeleteNotFound()
    {

        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->deleteAction,
            'id' => 2,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
        ]);

        $this->assertDatabaseHas('user_role', [
            'user_id' => 1,
            'role_id' => [1, 2]
        ]);
    }

    public function testDeleteWithoutId()
    {

        $this->createDummyUser();

        $this->call('POST', '/api/user', array(
            'action' => $this->deleteAction,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
        ]);

        $this->assertDatabaseHas('user_role', [
            'user_id' => 1,
            'role_id' => [1, 2]
        ]);
    }

    public function testDeleteWontEffectOtherUserWithSameRole()
    {
        /*
         * Delete should not interfere with other user with same role
         */

        $this->createDummyUser("1");
        $this->createDummyUser("2");

        $this->call('POST', '/api/user', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName . "1",
            'username' => $this->testUsername . "1"
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 2,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2"
        ]);

        $this->assertDatabaseMissing('user_role', [
            'user_id' => 1,
            'role_id' => 1,
        ]);

        $this->assertDatabaseHas('user_role', [
            'user_id' => 2,
            'role_id' => 1,
        ]);
    }

    public function testDeleteWontEffectOtherUserWithSameGroup()
    {
        /*
         * Delete should not interfere with other user with same group
         */

        $this->createDummyUser("1");
        $this->createDummyUser("2");

        $this->call('POST', '/api/user', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $this->testName . "1",
            'username' => $this->testUsername . "1"
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 2,
            'name' => $this->testName . "2",
            'username' => $this->testUsername . "2"
        ]);

        $this->assertDatabaseMissing('user_group', [
            'user_id' => 1,
            'group_id' => 1,
        ]);

        $this->assertDatabaseHas('user_group', [
            'user_id' => 2,
            'group_id' => 1,
        ]);
    }

    /*===========================================
             Role / Group Deleted section
    ===========================================*/

    public function testRoleOwnGotDeleted()
    {
        $this->createDummyUser();

        // Delete the role
        $this->call('POST', '/api/role', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername
        ]);

        $this->assertDatabaseMissing('user_role', [
            'user_id' => 1,
            'role_id' => 1.
        ]);
    }

    public function testGroupOwnGotDeleted()
    {
        $this->createDummyUser();

        // Delete the group
        $this->call('POST', '/api/group', array(
            'action' => $this->deleteAction,
            'id' => 1,
        ));

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $this->testName,
            'username' => $this->testUsername
        ]);

        $this->assertDatabaseMissing('user_group', [
            'user_id' => 1,
            'group_id' => 1.
        ]);
    }

    /*===========================================
                Other section
    ===========================================*/

    public function testFetchAll()
    {

        $this->createDummyUser(1);
        $this->createDummyUser(2);
        $this->createDummyUser(3);

        $this->assertDatabaseHas('users', [
            'id' => [1, 2, 3],
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => 4,
        ]);

        $response = $this->call('POST', '/api/user', array(
            'action' => "fetchAll",
        ));

        $response->assertSee($this->testName . "1");
        $response->assertSee($this->testName . "2");
        $response->assertSee($this->testName . "3");
    }

}
