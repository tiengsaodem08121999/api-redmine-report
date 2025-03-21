<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedmineLogController;
use App\Http\Controllers\ReportController;

Route::get('/redmine-log', [RedmineLogController::class, 'fetchLogTime'])->name('redmine');
