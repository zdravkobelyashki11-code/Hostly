<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\HostDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GuestDashboardController;

// My code starts here

Route::get('/', [PropertyController::class, 'index']);
Route::get('/search', [PropertyController::class, 'search'])->name('properties.search');

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::post('/properties/{property}/book', [BookingController::class, 'store'])->name('bookings.store');
});



// Host routes
Route::middleware(['auth', 'host'])->prefix('host')->name('host.')->group(function () {
    Route::get('/dashboard', [HostDashboardController::class, 'index'])->name('dashboard');
    Route::resource('properties', HostDashboardController::class)->except(['index', 'show']);
    Route::delete('/properties/{property}/images/{image}', [HostDashboardController::class, 'destroyImage'])->name('properties.images.destroy');
    
    // Booking approvals
    Route::post('/bookings/{booking}/approve', [HostDashboardController::class, 'approveBooking'])->name('bookings.approve');
    Route::post('/bookings/{booking}/reject', [HostDashboardController::class, 'rejectBooking'])->name('bookings.reject');
    
    // Guest profile viewing
    Route::get('/guests/{guest}', [HostDashboardController::class, 'showGuest'])->name('guests.show');
});

// Guest routes
Route::middleware(['auth'])->prefix('guest')->name('guest.')->group(function () {
    Route::get('/dashboard', [GuestDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings/{booking}/edit', [GuestDashboardController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [GuestDashboardController::class, 'update'])->name('bookings.update');
});

// Admin routes
Route::get('/admin', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.authenticate');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User CRUD
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    
    // Property CRUD
    Route::get('/properties/create', [AdminController::class, 'createProperty'])->name('properties.create');
    Route::post('/properties', [AdminController::class, 'storeProperty'])->name('properties.store');
    Route::get('/properties/{property}/edit', [AdminController::class, 'editProperty'])->name('properties.edit');
    Route::put('/properties/{property}', [AdminController::class, 'updateProperty'])->name('properties.update');
    Route::delete('/properties/{property}', [AdminController::class, 'destroyProperty'])->name('properties.destroy');
});