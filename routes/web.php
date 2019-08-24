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

Route::get('/', function () {
    return view('web.dashboard');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('me', 'User\UserController@index');
});

Auth::routes();

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
