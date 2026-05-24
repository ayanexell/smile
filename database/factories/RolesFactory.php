<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RolesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_role' => fake()->unique()->randomElement(['User', 'Koordinator', 'Admin', 'Super Admin']),
        ];
    }

    /**
     * Indicate that the role is super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => 'Super Admin',
        ]);
    }

    /**
     * Indicate that the role is admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => 'Admin',
        ]);
    }

    public function koordinators(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => 'Koordinator',
        ]);
    }



    /**
     * Indicate that the role is user.
     */
    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_role' => 'User',
        ]);
    }
}
