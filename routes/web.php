<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public welcome page - redirect logged-in users to todos
Route::get('/', function () {
    // If user is logged in, redirect to todos
    if (auth()->check()) {
        return redirect()->route('todos');
    }

    // Show welcome page to guests
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// Protected routes - require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/todos', function () {
        return Inertia::render('Todos');
    })->name('todos');
});

// Profile routes - require authentication
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
