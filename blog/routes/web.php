<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CiaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlacementReadinessController;
use App\Http\Controllers\CareerIntelligenceController;
use App\Http\Controllers\InternshipMatchingController;

Route::get(
    '/internship-matches',
    [InternshipMatchingController::class, 'index']
)->name('internship-matches');

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

    // W11D2 - CIA Skill Gap with Groq -> OpenRouter fallback
    Route::post(
        '/cia-career/skill-gap',
        [CiaController::class, 'skillGap']
    )->name('cia-career.skill-gap');

    // W10D2 - Placement & Interview Readiness
    Route::post(
        '/cia-career/readiness',
        [PlacementReadinessController::class, 'calculate']
    )->name('cia-career.readiness');

    // W10D3 - Career Intelligence Report
    Route::post(
        '/career-intelligence/generate',
        [CareerIntelligenceController::class, 'generate']
    )->name('career-intelligence.generate');

    Route::get(
        '/career-intelligence/{report}',
        [CareerIntelligenceController::class, 'show']
    )->name('career-intelligence.show');

    Route::get(
        '/career-intelligence/{report}/download',
        [CareerIntelligenceController::class, 'download']
    )->name('career-intelligence.download');
});

require __DIR__.'/auth.php';
