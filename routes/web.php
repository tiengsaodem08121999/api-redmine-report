<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedmineLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::middleware(['auth'])->group(function () {
    Route::get('/', [RedmineLogController::class, 'fetchLogTime'])->name('report');
    Route::post('/executeReport', [RedmineLogController::class, 'executeReport'])->name('executeReport');
    Route::post('/logtime', [RedmineLogController::class, 'LogTime'])->name('logtime');
    Route::get('/create_task', [RedmineLogController::class, 'createTask'])->name('create_task');
    Route::post('/execute_create_task', [RedmineLogController::class, 'executeCreateTask'])->name('execute_create_task');
    Route::get('/check_logtime', [RedmineLogController::class, 'checkLogtime'])->name('check_logtime');
    Route::get('/logtime_for_this_month', [RedmineLogController::class, 'logtimeForThisMonth'])->name('logtime_for_this_month');
    Route::post('/execute_logtime_for_this_month', [RedmineLogController::class, 'executeLogtimeForThisMonth'])->name('execute_logtime_for_this_month');
    Route::post('/delete_spent_time', [RedmineLogController::class, 'deleteSpentTime'])->name('delete_spent_time');
    Route::get('/PCV', [RedmineLogController::class, 'PCV'])->name('pcv');
    Route::post('/Update_PCV', [RedmineLogController::class, 'UpdatePCV'])->name('update_PCV');
    Route::get('/issue_done', [RedmineLogController::class, 'issueDone'])->name('issue_done');
    Route::post('/close_issue', [RedmineLogController::class, 'closeIssue'])->name('close_ssue');
// });