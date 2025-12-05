<?php

use App\Http\Controllers\GoalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes are included via Laravel Breeze
require __DIR__.'/auth.php';

// Protected routes - require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Goal Management - Resource routes
    Route::resource('goals', GoalController::class);

    // ✅ BUTTON ACTION ROUTES (MATCH UI EXACTLY)

    // Mark as Done
    Route::post('/goals/{goal}/complete', [GoalController::class, 'complete'])
        ->name('goals.done');

    // Reopen
    Route::post('/goals/{goal}/reopen', [GoalController::class, 'reopen'])
        ->name('goals.reopen');

    // Hide / Archive (only ONE route, not two)
    Route::post('/goals/{goal}/archive', [GoalController::class, 'archive'])
        ->name('goals.archive');

    // Increment progress (Start / Add +1)
    Route::post('/goals/{goal}/increment', [GoalController::class, 'increment'])
        ->name('goals.increment');

    // Inline progress edit form
    Route::post('/goals/{goal}/update-progress', [GoalController::class, 'increment'])
        ->name('goals.updateProgress');

    // Batch complete
    Route::post('/goals/batch-complete', [GoalController::class, 'batchComplete'])
        ->name('goals.batch-complete');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});
