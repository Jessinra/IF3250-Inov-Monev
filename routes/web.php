<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'PagesController@login');

Route::get('/dashboard', function () {
    // TODO: change this after dashboard is done, put it inside method
    return view('home');
});


Route::get('home', 'HomeController@index')->name('home');
Route::get('register', 'Auth\\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\\RegisterController@registerHandler')->name('registerPost');

Route::get('/user', 'UserController@userDashboard');
Route::post('/user', 'UserController@userManagementHandler');

Route::get('/group', 'GroupController@groupDashboard');
Route::post('/group', 'GroupController@groupManagementHandler');

Route::get('/role', 'RoleController@roleDashboard');
Route::post('/role', 'RoleController@roleManagementHandler');

Route::get('/permission', 'PermissionController@permissionDashboard');
Route::post('/permission', 'PermissionController@permissionManagementHandler');

