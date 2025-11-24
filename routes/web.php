<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoalController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('goals', GoalController::class);
});

// goals actions
Route::middleware(['auth'])->group(function () {
    Route::post('/goals/{goal}/done', [App\Http\Controllers\GoalController::class, 'markDone'])->name('goals.done');
    Route::post('/goals/{goal}/reopen', [App\Http\Controllers\GoalController::class, 'reopen'])->name('goals.reopen');
    Route::patch('/goals/{goal}/progress', [App\Http\Controllers\GoalController::class, 'updateProgress'])->name('goals.updateProgress');
});

require __DIR__.'/auth.php';
