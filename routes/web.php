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
    return view('welcome');
});

Route::post('user/upload', 'UserController@upload');


Auth::routes();

Route::get('/vue', 'UserController@vue')->name('vue');


Route::group(['middleware' => 'auth:web'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/init', 'HomeController@init');

    Route::group(['middleware' => 'throttle:60,1'], function () {
        Route::post('/say', 'HomeController@say');
    });

    Route::get('/test', 'HomeController@usersByGroup');
    Route::get('user/profile', 'UserController@profile')->name('user/profile');
    Route::get('userInfo', 'UserController@userInfo');
    Route::post('user/store', 'UserController@store');
});


