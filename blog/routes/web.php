<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CiaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlacementReadinessController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// CIA Career
Route::middleware('auth')->group(function () {

    Route::get('/cia-career', [CiaController::class, 'index'])
        ->name('cia-career');

    // W10D2 - Placement & Interview Readiness
    Route::post(
        '/cia-career/readiness',
        [PlacementReadinessController::class, 'calculate']
    )->name('cia-career.readiness');
});

require __DIR__.'/auth.php';