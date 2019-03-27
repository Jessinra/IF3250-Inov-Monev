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

// Auth::routes();

// Route::get('/{any}', 'PagesController@index')->where('any', '.*');

// Route::put('/permissions', 'PermissionController@updatePermission');
// Route::get('/{any}', function(){
//     return view('layouts.app');
// })->where('any', '.*');

Auth::routes();

Route::get('/{any}', 'PagesController@index')->where('any', '.*');
Route::post('/permissions', 'PermissionController@permissionManagementHandler');
Route::put('/permissions', 'PermissionController@updatePermission');