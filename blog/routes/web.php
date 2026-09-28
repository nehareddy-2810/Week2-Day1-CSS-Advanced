<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CiaController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('cia-career')->name('cia_career.')->group(function () {
    Route::get('/dashboard', function () {
        return 'CIA Career Intelligence dashboard — implementation pending.';
    })->name('dashboard');

    Route::get('/profile', function () {
        return 'CIA Career Intelligence candidate profile — implementation pending.';
    })->name('profile');

    Route::get('/skill-gap', [CiaController::class, 'index'])
        ->middleware('auth')
        ->name('skill-gap');

    Route::get('/career-pathway', function () {
        return 'CIA Career Intelligence career pathway — implementation pending.';
    })->name('career-pathway');

    Route::get('/history', function () {
        return 'CIA Career Intelligence assessment history — implementation pending.';
    })->name('history');

    Route::get('/interview-coach', function () {
        return 'CIA Career Intelligence interview coach — implementation pending.';
    })->name('interview-coach');

    Route::get('/report', function () {
        return 'CIA Career Intelligence report — implementation pending.';
    })->name('report');
});

Route::post('/candidate/cia/career/skill-gap', [CiaController::class, 'skillGap'])
    ->middleware('auth')
    ->name('candidate.cia.career.skill-gap');

require __DIR__.'/auth.php';
