<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    CRUD::resource('software', 'SoftwareCrudController');
    CRUD::resource('job', 'JobCrudController');
    CRUD::resource('category', 'CategoryCrudController');
    CRUD::resource('bofh', 'BofhCrudController');
    CRUD::resource('telegram', 'TelegramCrudController');
    CRUD::resource('telegram_canal', 'TelegramCanalCrudController');
    CRUD::resource('quote', 'QuoteCrudController');
}); // this should be the absolute last line of this file