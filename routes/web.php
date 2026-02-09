<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\AuthController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Home = Login (Volt)
Volt::route('/', 'auth.login')->name('login');

// Optional: redirect /login → /
Route::redirect('/login', '/');

// Google Auth Routes
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::post('/logout', [GoogleAuthController::class, 'logout'])
    ->name('logout');

// Protected Dashboard
Volt::route('/dashboard', 'dashboard')
    ->middleware('auth')
    ->name('dashboard');

require __DIR__ . '/settings.php';
