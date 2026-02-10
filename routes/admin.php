<?php

/*
  |--------------------------------------------------------------------------
  | ADMIN ROUTES
  |--------------------------------------------------------------------------
  */

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'role:admin'])
  ->prefix('admin')
  ->name('admin.')
  ->group(function () {

    Volt::route('/dashboard', 'admin.dashboard')
      ->name('dashboard');

    Volt::route('/faculty', 'admin.faculty-list')
      ->name('faculty');
  });
