<?php

use App\Models\Roles;
use App\Models\User;
use App\Models\Departemens;
use App\Models\Inventaris;
use App\Models\Laporan;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('complete user relationship chain using factory approach', function () {
    // Jalankan seeder untuk memastikan role dan departemen dasar tersedia
    $this->seed([
        // \Database\Seeders\RoleSeeder::class,
        \Database\Seeders\DepartemenSeeder::class,
    ]);

    // Ambil role Admin yang sudah ada (dibuat oleh seeder)
    $role = Roles::factory()->create();
    // $departemen = Departemens::factory()->create();

    // Ambil departemen KAMTIB yang sudah ada
    $departemen = Departemens::where('singkatan', 'KAMTIB')->firstOrFail();

    // Buat user dengan factory, override role_id dan departemen_id
    $user = User::factory()->create([
        'role_id'       => $role->id_role,
        'departemen_id' => $departemen->id_departemen,
    ]);

    // Buat 5 inventaris milik user menggunakan factory
    $inventarisItems = Inventaris::factory()
        ->count(5)
        ->create(['user_id' => $user->id_user]);

    // Buat 3 laporan milik user
    $laporans = Laporan::factory()
        ->count(3)
        ->create(['user_id' => $user->id_user]);

    // Buat 2 peminjaman untuk inventaris pertama
    $peminjamans = Peminjaman::factory()
        ->count(2)
        ->create([
            'user_id'       => $user->id_user,
            'inventaris_id' => $inventarisItems->first()->id_inventaris,
        ]);

    // Muat ulang relasi user untuk memastikan data terbaru
    $user->load(['inventaris', 'laporans', 'peminjamans']);

    // Assert relationships
    expect($user->inventaris)->toHaveCount(5);
    expect($user->laporans)->toHaveCount(3);
    expect($user->peminjamans)->toHaveCount(2);

    // Verify data integrity di database
    $this->assertDatabaseCount('inventaris', 5);
    $this->assertDatabaseCount('laporans', 3);
    $this->assertDatabaseCount('peminjaman', 2);

    // Pastikan foreign key terisi dengan benar
    foreach ($user->inventaris as $item) {
        expect($item->user_id)->toBe($user->id_user);
    }
});
