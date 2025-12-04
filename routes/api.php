<?php

use App\Http\Controllers\Api\DeleteTodoController;
use App\Http\Controllers\Api\ListTodosController;
use App\Http\Controllers\Api\ShowTodoController;
use App\Http\Controllers\Api\StoreTodoController;
use App\Http\Controllers\Api\UpdateTodoController;
use Illuminate\Support\Facades\Route;

// Use 'web' middleware for same-domain SPA
Route::middleware(['web', 'auth'])->group(function () {
   Route::get('/todos', ListTodosController::class);
   Route::post('/todos', StoreTodoController::class);
   Route::get('/todos/{todo}', ShowTodoController::class);
   Route::put('/todos/{todo}', UpdateTodoController::class);
   Route::delete('/todos/{todo}', DeleteTodoController::class);
});
