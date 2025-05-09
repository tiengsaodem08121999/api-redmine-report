<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedmineLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [RedmineLogController::class, 'fetchLogTime'])->name('redmine');
});
