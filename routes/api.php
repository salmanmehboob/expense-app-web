<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user/info', [AuthController::class, 'userInfo']);
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('{id}', [CategoryController::class, 'show']);
        Route::post('{id}/update', [CategoryController::class, 'update']);
        Route::delete('{id}', [CategoryController::class, 'destroy']);
        Route::post('{id}/restore', [CategoryController::class, 'restore']); // Optional restore
    });

    Route::apiResource('expenses', \App\Http\Controllers\Api\ExpenseController::class);


});
