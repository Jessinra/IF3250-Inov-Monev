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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/createAdmin', 'Auth\\RegisterController@registerHandler');


//http://127.0.0.1:8000/createAdmin?name=admin&username=admin&email=admin@admin.com&password=12341234&password_confirmation=12341234&user_type=Admin&dinas_id=1