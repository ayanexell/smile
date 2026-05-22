<?php

use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('role has many users', function () {
    $role = Roles::factory()->create();
    $users = User::factory()->count(3)->create(['role_id' => $role->id_role]);

    expect($role->users)->toHaveCount(3);
    expect($role->users->first())->toBeInstanceOf(User::class);
});

test('role has unique nama role', function () {
    Roles::factory()->create(['nama_role' => 'Another Admin']);

    expect(fn() => Roles::factory()->create(['nama_role' => 'Another Admin']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('role factory creates valid data', function () {
    $role = Roles::factory()->create();

    expect($role->nama_role)->toBeString();
    expect($role->nama_role)->not->toBeEmpty();
});
