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

Route::get('/get-me', 'TelegramController@getMe');
Route::get('/random', 'TelegramController@random');
Route::post(
    env('TELEGRAM_WEBHOOK_ROUTE', '/webhook'),
    'TelegramController@handleRequest'
);

