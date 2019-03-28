<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    private $createAction = "create";

    private $testPermissionName = "test permission";
    private $testPermissionDesc = 'permission for test only';
    private $testGroupName = "test group";
    private $testGroupDesc = 'group for test only';
    private $testRoleName = "test role";
    private $testRoleDesc = 'role for test only';
    private $testPermissionCount = 2;
    private $testName = "test user";
    private $testUsername = 'test username';
    private $testEmail = "testemail@email.com";
    private $testPassword = "test pass";

    protected function createDummyPermission($copy = "")
    {

        $this->call('POST', '/api/permissions', array(
            'action' => $this->createAction,
            'name' => $this->testPermissionName . $copy,
            'description' => $this->testPermissionDesc . $copy
        ));
    }

    protected function createDummyGroup($copy = "")
    {

        $this->call('POST', '/group', array(
            'action' => $this->createAction,
            'name' => $this->testGroupName . $copy,
            'description' => $this->testGroupDesc . $copy
        ));
    }

    protected function createDummyRole($copy = "")
    {

        $this->createDummyPermission("1");
        $this->createDummyPermission("2");

        $this->call('POST', '/role', array(
            'action' => $this->createAction,
            'name' => $this->testRoleName . $copy,
            'description' => $this->testRoleDesc . $copy,
            'permissionCount' => $this->testPermissionCount,
            'permission0' => 1,
            'permission1' => 2
        ));
    }

    protected function createDummyUser($copy = "")
    {
        $this->createDummyRole();
        $this->createDummyGroup();

        $this->call('POST', '/user', array(
            'action' => $this->createAction,
            'name' => $this->testName . $copy,
            'username' => $this->testUsername . $copy,
            'email' => $this->testEmail . $copy,
            'password' => $this->testPassword,
            'password_confirmation' => $this->testPassword,
            'roleId' => 1,
            'groupId' => 1,
        ));
    }
}
