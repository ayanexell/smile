<?php

namespace Database\Factories;

use App\Models\Departemens;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Roles;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => Roles::factory()->create(['nama_role' => 'User'])->id_role,
            // 'departemen_id' => Departemens::factory(),
            'nama_lengkap' => fake()->name(),
            'nik' => fake()->unique()->numerify('################'),
            'tgl_lahir' => fake()->date('Y-m-d', '-18 years'),
            'jenis_kelamin' => fake()->randomElement(['laki-laki', 'perempuan']),
            'email' => fake()->unique()->safeEmail(),
            'alamat' => fake()->address(),
            'pekerjaan' => fake()->jobTitle(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    // State methods untuk set role dan departemen
public function superAdmin(): static
{
    return $this->state(function (array $attributes) {
        $role = Roles::firstOrCreate(['nama_role' => 'Super Admin']);
        return ['role_id' => $role->id_role];
    });
}

public function admin(): static
{
    return $this->state(function (array $attributes) {
        $role = Roles::firstOrCreate(['nama_role' => 'Admin']);
        return ['role_id' => $role->id_role];
    });
}

public function regularUser(): static
{
    return $this->state(function (array $attributes) {
        $role = Roles::firstOrCreate(['nama_role' => 'User']);
        return ['role_id' => $role->id_role];
    });
}

    /**
     * Set user with specific departemen.
     */
    public function withDepartemen(string $singkatan): static
    {
        return $this->state(fn (array $attributes) => [
            'departemen_id' => Departemens::factory()->{$singkatan}(),
        ]);
    }

    /**
     * Set user without departemen.
     */
    public function withoutDepartemen(): static
    {
        return $this->state(fn (array $attributes) => [
            'departemen_id' => null,
        ]);
    }

    /**
     * Set specific gender.
     */
    public function lakiLaki(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_kelamin' => 'laki-laki',
        ]);
    }

    public function perempuan(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_kelamin' => 'perempuan',
        ]);
    }
}
