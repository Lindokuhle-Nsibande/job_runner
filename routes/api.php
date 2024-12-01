<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobLogController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

route::get('/job-logs', [JobLogController::class, 'getLogs'])->name('api.job_logs');
Route::post('/job-logs/{id}/cancel', [JobLogController::class, 'cancelJob'])->name('job_logs.cancel');