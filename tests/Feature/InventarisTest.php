<?php

use App\Models\Inventaris;
use App\Models\User;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('inventaris belongs to user', function () {
    $user = User::factory()->create();
    $inventaris = Inventaris::factory()->create(['user_id' => $user->id_user]);

    expect($inventaris->user)->toBeInstanceOf(User::class);
    expect($inventaris->user->id_user)->toBe($user->id_user);
});

test('inventaris has many peminjamans', function () {
    $inventaris = Inventaris::factory()->create();
    $user = User::factory()->create();

    $peminjamans = Peminjaman::factory()->count(3)->create([
        'inventaris_id' => $inventaris->id_inventaris,
        'user_id' => $user->id_user
    ]);

    expect($inventaris->peminjamans)->toHaveCount(3);
    expect($inventaris->peminjamans->first())->toBeInstanceOf(Peminjaman::class);
});

test('inventaris can be borrowed or not', function () {
    $borrowable = Inventaris::factory()->dapatDipinjam()->create();
    $notBorrowable = Inventaris::factory()->tidakDapatDipinjam()->create();

    expect($borrowable->dpt_dipinjam)->toBeTrue();
    expect($notBorrowable->dpt_dipinjam)->toBeFalse();
});

test('inventaris factory states work correctly', function () {
    $baik = Inventaris::factory()->baik()->create();
    $rusak = Inventaris::factory()->rusak()->create();
    $elektronik = Inventaris::factory()->elektronik()->create();
    $furniture = Inventaris::factory()->furniture()->create();

    expect($baik->kondisi)->toBe('baik');
    expect($rusak->kondisi)->toBe('rusak');
    expect($elektronik->tipe)->toBe('Elektronik');
    expect($furniture->tipe)->toBe('Furniture');
});

test('inventaris memiliki atribut yang lengkap', function () {
    $inventaris = Inventaris::factory()->create();

    expect($inventaris->nama_barang)->toBeString();
    expect($inventaris->jumlah)->toBeInt();
    expect($inventaris->kondisi)->toBeIn(['baik', 'rusak']);
    expect($inventaris->warna)->toBeString();
    expect($inventaris->tipe)->toBeString();
    expect($inventaris->img_path)->toBeString();
    expect($inventaris->dpt_dipinjam)->toBeBool();
});
