<?php

use App\Http\Controllers\Api\Categories\AttachCategoryController;
use App\Http\Controllers\Api\Categories\DeleteCategoryController;
use App\Http\Controllers\Api\Categories\DetachCategoryController;
use App\Http\Controllers\Api\Categories\ListCategoriesController;
use App\Http\Controllers\Api\Categories\ShowCategoryController;
use App\Http\Controllers\Api\Categories\StoreCategoryController;
use App\Http\Controllers\Api\Categories\UpdateCategoryController;
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

   // Category routes
   Route::get('/categories', ListCategoriesController::class);
   Route::post('/categories', StoreCategoryController::class);
   Route::get('/categories/{category}', ShowCategoryController::class);
   Route::put('/categories/{category}', UpdateCategoryController::class);
   Route::delete('/categories/{category}', DeleteCategoryController::class);

   // Attach/Detach categories to todos
   Route::post('/todos/{todo}/categories/{category}', AttachCategoryController::class);
   Route::delete('/todos/{todo}/categories/{category}', DetachCategoryController::class);
});
