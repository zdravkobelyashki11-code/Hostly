<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\HostDashboardController;

// My code starts here

Route::get('/', [PropertyController::class, 'index']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
});

Route::middleware(['auth', 'host'])->prefix('host')->name('host.')->group(function () {
    Route::get('/dashboard', [HostDashboardController::class, 'index'])->name('dashboard');
    Route::resource('properties', HostDashboardController::class)->except(['index', 'show']);
    Route::delete('/properties/{property}/images/{image}', [HostDashboardController::class, 'destroyImage'])->name('properties.images.destroy');
});