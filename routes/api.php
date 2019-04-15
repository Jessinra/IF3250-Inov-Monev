<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;

Route::post('login', 'UserController@login');

Route::post('/permissions', 'PermissionController@permissionManagementHandler');
Route::post('/role', 'RoleController@roleManagementHandler');
Route::post('/group', 'GroupController@groupManagementHandler');
Route::post('/user', 'UserController@userManagementHandler');

Route::post('/stage', 'StageController@stageManagementHandler');
