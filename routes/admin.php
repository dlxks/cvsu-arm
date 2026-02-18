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

        // Faculty Management Routes
        Volt::route('/faculty', 'admin.faculty.index')->name('faculty');
        Volt::route('/faculty/{faculty}/edit', 'admin.faculty.edit')->name('faculty.edit');
        Volt::route('/faculty/{faculty}', 'admin.faculty.show')->name('faculty.show');

        // Volt::route('/faculty', 'pages.admin.faculty.index')->name('faculty.index');
        // Volt::route('/faculty/{faculty}/edit', 'pages.admin.faculty.edit')->name('faculty.edit');
        // Volt::route('/faculty/{faculty}', 'pages.admin.faculty.show')->name('faculty.show');
        // Volt::route('/faculty/create', 'pages.admin.faculty.create')->name('faculty.create');
    });
