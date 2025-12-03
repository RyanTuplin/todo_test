<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// /*
// |--------------------------------------------------------------------------
// | API Routes
// |--------------------------------------------------------------------------
// |
// | Here is where you can register API routes for your application. These
// | routes are loaded by the RouteServiceProvider and all of them will
// | be assigned to the "api" middleware group. Make something great!
// |
// */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

use App\Http\Controllers\Api\UpdateTodoController;
use App\Http\Controllers\Api\StoreTodoController;
use App\Http\Controllers\Api\ListTodosController;
use App\Http\Controllers\Api\ShowTodoController;
use App\Http\Controllers\Api\DeleteTodoController;
use Illuminate\Support\Facades\Route;

Route::get('/todos', ListTodosController::class);
Route::post('/todos', StoreTodoController::class);
Route::get('/todos/{todo}', ShowTodoController::class);
Route::put('/todos/{todo}', UpdateTodoController::class);
Route::delete('/todos/{todo}', DeleteTodoController::class);
