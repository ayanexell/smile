use Illuminate\Support\Facades\Hash;
<?php

use App\Models\Departemens;
use App\Models\User;
use App\Models\Roles;
use App\Models\Inventaris;
use App\Models\Laporan;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user belongs to role', function () {
    $role = Roles::where('nama_role','Super Admin')->first();
    $user = User::factory()->create(['role_id' => $role->id_role]);

    expect($user->role)->toBeInstanceOf(Roles::class);
    expect($user->role->id_role)->toBe($role->id_role);
});

test('user belongs to departemen', function () {
    $departemen = Departemens::factory()->create();
    $user = User::factory()->create(['departemen_id' => $departemen->id_departemen]);

    expect($user->departemen)->toBeInstanceOf(Departemens::class);
    expect($user->departemen->id_departemen)->toBe($departemen->id_departemen);
});

test('user can have null departemen', function () {
    $user = User::factory()->withoutDepartemen()->create();

    expect($user->departemen_id)->toBeNull();
    expect($user->departemen)->toBeNull();
});

test('user has many inventaris', function () {
    $user = User::factory()->create();
    $inventaris = Inventaris::factory()->count(5)->create(['user_id' => $user->id_user]);

    expect($user->inventaris)->toHaveCount(5);
    expect($user->inventaris->first())->toBeInstanceOf(Inventaris::class);
});

test('user has many laporans', function () {
    $user = User::factory()->create();
    $laporans = Laporan::factory()->count(3)->create(['user_id' => $user->id_user]);

    expect($user->laporans)->toHaveCount(3);
    expect($user->laporans->first())->toBeInstanceOf(Laporan::class);
});

test('user has many peminjamans', function () {
    $user = User::factory()->create();
    $inventaris = Inventaris::factory()->create();
    $peminjaman = Peminjaman::factory()->count(3)->create([
        'user_id' => $user->id_user,
        'inventaris_id' => $inventaris->id_inventaris
    ]);

    expect($user->peminjamans)->toHaveCount(3);
    expect($user->peminjamans->first())->toBeInstanceOf(Peminjaman::class);
});

test('user email is unique', function () {
    User::factory()->create(['email' => 'test@example.com']);

    expect(fn() => User::factory()->create(['email' => 'test@example.com']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('user nik is unique and 16 characters', function () {
    User::factory()->create(['nik' => '3201010101900001']);

    expect(fn() => User::factory()->create(['nik' => '3201010101900001']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('user factory states work correctly', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->regularUser()->create();

    expect($superAdmin->role->nama_role)->toBe('Super Admin');
    expect($admin->role->nama_role)->toBe('Admin');
    expect($regularUser->role->nama_role)->toBe('User');
});
