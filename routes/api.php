<?php

use App\Http\Middleware\ValidateApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(ValidateApiToken::class)->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('projects', \App\Http\Controllers\Api\ProjectController::class);
    });
});
