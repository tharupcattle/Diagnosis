<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/check-symptom', [DashboardController::class, 'checkSymptom'])->name('check.symptom');
Route::post('/simulate', [DashboardController::class, 'simulate'])->name('simulate');

// Cattle management routes
Route::get('/cattle/{id}', [DashboardController::class, 'showCattle'])->name('cattle.show');

// Alert routes
Route::post('/alerts/{id}/resolve', [DashboardController::class, 'resolveAlert'])->name('alert.resolve');

// Analytics
Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

// AI Chat
Route::post('/ai-chat', [DashboardController::class, 'aiChat'])->name('ai.chat');
