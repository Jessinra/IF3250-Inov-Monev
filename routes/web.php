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

Route::get('/{any}', 'PagesController@index')->where('any', '.*');
Route::post('/permissions', 'PermissionController@createNewPermission');
Route::put('/permissions', 'PermissionController@updatePermission');