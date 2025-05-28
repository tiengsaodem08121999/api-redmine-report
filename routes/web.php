<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedmineLogController;

Route::get('/', [RedmineLogController::class, 'logtimeForThisMonth'])->name('logtime_for_this_month');
Route::post('/', [RedmineLogController::class, 'executeLogtimeForThisMonth'])->name('execute_logtime_for_this_month');
Route::post('/add_key_developer', [RedmineLogController::class, 'addKeyDeveloper'])->name('add_key_developer');
