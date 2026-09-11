use Carbon\Carbon;
<?php

use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('peminjaman belongs to user', function () {
    $user = User::factory()->create();
    $inventaris = Inventaris::factory()->create();
    $peminjaman = Peminjaman::factory()->create([
        'user_id' => $user->id_user,
        'inventaris_id' => $inventaris->id_inventaris
    ]);

    expect($peminjaman->user)->toBeInstanceOf(User::class);
    expect($peminjaman->user->id_user)->toBe($user->id_user);
});

test('peminjaman belongs to inventaris', function () {
    $inventaris = Inventaris::factory()->create();
    $peminjaman = Peminjaman::factory()->create([
        'inventaris_id' => $inventaris->id_inventaris
    ]);

    expect($peminjaman->inventaris)->toBeInstanceOf(Inventaris::class);
    expect($peminjaman->inventaris->id_inventaris)->toBe($inventaris->id_inventaris);
});

test('peminjaman can be late', function () {
    $tepatWaktu = Peminjaman::factory()->tepatWaktu()->create();
    $terlambat = Peminjaman::factory()->terlambat()->create();

    expect($tepatWaktu->lambat)->toBeFalse();
    expect($terlambat->lambat)->toBeTrue();
});

test('peminjaman has correct dates', function () {
    $peminjaman = Peminjaman::factory()->create();

    expect($peminjaman->tgl_peminjaman)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($peminjaman->tgl_pengembalian)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($peminjaman->tgl_pengembalian->gt($peminjaman->tgl_peminjaman))->toBeTrue();
});

test('peminjaman factory states work correctly', function () {
    $user = User::factory()->create();
    $inventaris = Inventaris::factory()->create();

    $peminjaman = Peminjaman::factory()
        ->oleh($user)
        ->untuk($inventaris)
        ->denganHibah(2)
        ->create();

    expect($peminjaman->user_id)->toBe($user->id_user);
    expect($peminjaman->inventaris_id)->toBe($inventaris->id_inventaris);
    expect($peminjaman->hibah)->toBe(2);
});
