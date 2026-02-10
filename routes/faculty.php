<?php

/*
|--------------------------------------------------------------------------
| FACULTY ROUTES
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'role:faculty'])
  ->prefix('faculty')
  ->name('faculty.')
  ->group(function () {

    Volt::route('/dashboard', 'faculty.dashboard')
      ->name('dashboard');

    // other faculty-only routes here
  });
