<?php

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('laporan belongs to user', function () {
    $user = User::factory()->create();
    $laporan = Laporan::factory()->create(['user_id' => $user->id_user]);

    expect($laporan->user)->toBeInstanceOf(User::class);
    expect($laporan->user->id_user)->toBe($user->id_user);
});

test('laporan has correct statuses', function () {
    $pending = Laporan::factory()->pending()->create();
    $disetujui = Laporan::factory()->disetujui()->create();
    $ditolak = Laporan::factory()->ditolak()->create();

    expect($pending->status)->toBe('pending');
    expect($disetujui->status)->toBe('disetujui');
    expect($ditolak->status)->toBe('ditolak');
});

test('laporan factory states work correctly', function () {
    $laporan = Laporan::factory()->forMonth('Januari 2024')->create();

    expect($laporan->month)->toBe('Januari 2024');
});
