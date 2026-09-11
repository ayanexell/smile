<?php

use App\Models\Departemens;
use App\Models\Roles;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Pastikan role exists
    if (Roles::count() === 0) {
        Roles::create(['nama_role' => 'Super Admin']);
        Roles::create(['nama_role' => 'Admin']);
        Roles::create(['nama_role' => 'User']);
    }
});

test('departemen has many users', function () {
    $departemen = Departemens::create([
        'nama_departemen' => 'Keamanan dan Ketertiban',
        'singkatan' => 'KAMTIB'
    ]);

    $role = Roles::where('nama_role', 'User')->first();

    $users = [];
    for ($i = 0; $i < 3; $i++) {
        $users[] = User::factory()->create([
            'role_id' => $role->id_role,
            'departemen_id' => $departemen->id_departemen,
            'email' => "user{$i}@example.com",
            'nik' => fake()->unique()->numerify('################'),
        ]);
    }

    $departemen->refresh();

    expect($departemen->users)->toHaveCount(3);
    expect($departemen->users->first())->toBeInstanceOf(User::class);
});

test('departemen can have nullable singkatan', function () {
    $departemen = Departemens::create([
        'nama_departemen' => 'Departemen Tanpa Singkatan',
        'singkatan' => null
    ]);

    expect($departemen->singkatan)->toBeNull();
});

test('departemen factory creates valid data', function () {
    $departemen = Departemens::create([
        'nama_departemen' => 'Test Department',
        'singkatan' => 'TEST'
    ]);

    expect($departemen->nama_departemen)->toBeString();
    expect($departemen->nama_departemen)->toBe('Test Department');
    expect($departemen->singkatan)->toBe('TEST');
});
