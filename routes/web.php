<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::view('/', 'welcome')->name('home');
Route::livewire('list-inventaris', 'list-inventaris')->name('list-inventaris');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('dashboard/admin')->name('admin.')->group(function () {
        Route::livewire('admins', 'admin.manage-admins')->name('admins');
        Route::livewire('koordinators', 'admin.manage-koordinators')->name('koordinators');
        Route::livewire('users', 'admin.manage-users')->name('users');

        Route::livewire('departemens', 'admin.manage-departemen')->name('departemens');
        Route::livewire('inventaris', 'admin.manage-inventaris')->name('inventaris');
        Route::livewire('peminjaman', 'admin.manage-peminjaman')->name('peminjaman');
    });
    Route::prefix('dashboard/koordinator')->name('koordinator.')->group(function () {
        Route::livewire('inventaris', 'koordinator.inventaris.manage-inventaris')->name('inventaris');
    });
});

require __DIR__.'/settings.php';
