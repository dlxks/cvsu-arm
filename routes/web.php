<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
*/

Volt::route('/', 'auth.login')->name('login');
Route::redirect('/login', '/');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::post('/logout', [GoogleAuthController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| RESOLVER (SINGLE ENTRY POINT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/dashboard', function () {
    if (Auth::user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if (Auth::user()->hasRole('faculty')) {
        return redirect()->route('faculty.dashboard');
    }

    abort(403, 'You do not have a valid role assigned.');
})->name('dashboard.resolve');

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/faculty.php';
