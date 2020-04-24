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

Route::get(env('TELEGRAM_WEBHOOK_KEY') . '/get-me', 'TelegramController@getMe');
Route::get(env('TELEGRAM_WEBHOOK_KEY') . '/set-hook', 'TelegramController@setWebHook');
Route::get(env('TELEGRAM_WEBHOOK_KEY') . '/del-hook', 'TelegramController@removeWebHook');
Route::get(env('TELEGRAM_WEBHOOK_KEY') . '/random', 'TelegramController@random');
Route::post(env('TELEGRAM_WEBHOOK_KEY') . '/webhook', 'TelegramController@handleRequest');

