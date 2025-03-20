<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedmineLogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redmine-log', [RedmineLogController::class, 'fetchLogTime']);
