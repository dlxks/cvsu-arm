<?php

/*
  |--------------------------------------------------------------------------
  | ADMIN ROUTES
  |--------------------------------------------------------------------------
  */

use App\Livewire\Admin\FacultyProfileTable;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'role:admin'])
  ->prefix('admin')
  ->name('admin.')
  ->group(function () {

    Volt::route('/dashboard', 'admin.dashboard')
      ->name('dashboard');


    Volt::route('/faculty', 'admin.faculty.faculty-index')
      ->name('faculty');
  });
