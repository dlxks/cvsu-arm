<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// ---------------------- GUEST ROUTES ----------------------
Volt::route('/', 'auth.login')->name('login');

// Redirect /login -> / (Optional, since 'login' is now /)
Route::redirect('/login', '/');

// Google Auth
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');

// ---------------------- FACULTY ROUTES ----------------------
Route::middleware(['auth', 'role:faculty'])
    ->group(function () {
        // URL: /dashboard
        Volt::route('/dashboard', 'faculty.dashboard')
            ->name('dashboard');
    });

// ---------------------- ADMIN ROUTES ----------------------
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')       // Adds '/admin' to the URL
    ->name('admin.')        // Adds 'admin.' to the route name
    ->group(function () {

        // FIX: Change '/admin/dashboard' to '/dashboard'
        // Resulting URL: /admin/dashboard
        Volt::route('/dashboard', 'admin.dashboard')
            ->name('dashboard');
    });

require __DIR__ . '/settings.php';
