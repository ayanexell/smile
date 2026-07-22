<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('dashboard/admin')->name('admin.')->group(function () {
        Route::livewire('admins', 'admin.manage-admins')->name('admins');
        Route::livewire('koordinators', 'admin.manage-koordinators')->name('koordinators');
        Route::livewire('users', 'admin.manage-users')->name('users');

        Route::livewire('inventaris', 'admin.manage-inventaris')->name('inventaris');
    });
});

require __DIR__.'/settings.php';
