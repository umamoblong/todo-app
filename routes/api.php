<?php

use App\Http\Controllers\Api\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'API is working!';
});

Route::prefix('v1')->group(function () {
    Route::get('/todos', [TodoController::class, 'index']);
    Route::post('/todos', [TodoController::class, 'store']);
    Route::put('/todos/{id}/toggle', [TodoController::class, 'toggle']);
    Route::delete('/todos/{id}', [TodoController::class, 'destroy']);
});