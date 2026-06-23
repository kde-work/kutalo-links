<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\ShortLinkController;
use App\Http\Controllers\Api\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'show']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/links', [ShortLinkController::class, 'index']);
    Route::post('/links', [ShortLinkController::class, 'store']);
    Route::get('/links/{id}', [ShortLinkController::class, 'show']);
    Route::patch('/links/{id}', [ShortLinkController::class, 'update']);
    Route::patch('/links/{id}/destination', [ShortLinkController::class, 'updateDestination']);
    Route::delete('/links/{id}', [ShortLinkController::class, 'destroy']);

    Route::get('/links/{id}/statistics', [StatisticsController::class, 'show']);
});
