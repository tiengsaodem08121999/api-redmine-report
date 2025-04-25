<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedmineLogController;
use App\Http\Controllers\ReportController;

Route::get('/', [RedmineLogController::class, 'fetchLogTime'])->name('redmine');
Route::get('/pcv', [RedmineLogController::class, 'fetchPCV'])->name('pcv');
