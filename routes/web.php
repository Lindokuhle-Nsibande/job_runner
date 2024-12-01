<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\JobHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\JobLogController;

Route::get('/', function () {
    JobHelper::runBackgroundJob('App\\Jobs\\TestJob', 'execute', ['param1', 'param2']);
    return view('welcome');
});

Route::get('/test-job', function () {
    JobHelper::runBackgroundJob('App\\Jobs\\TestJob', 'execute', ['param1', 'param2']);
    return response()->json(['message' => 'Job execution started successfully.']);
});
Route::get('/job-logs', [JobLogController::class, 'index'])->name('job_logs.index');