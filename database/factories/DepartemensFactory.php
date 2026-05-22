<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Departemen>
 */
class DepartemensFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departemens = [
            ['nama_departemen' => 'Keamanan dan Ketertiban', 'singkatan' => 'KAMTIB'],
            ['nama_departemen' => 'Madrasah Diniyah', 'singkatan' => 'MADAL'],
            ['nama_departemen' => 'Peribadatan dan SKIA', 'singkatan' => 'TASKIA'],
            ['nama_departemen' => 'Pengajian Al-Qur\'an & Kitab', 'singkatan' => 'DEPAK'],
            ['nama_departemen' => 'Olahraga dan Kesenian', 'singkatan' => 'PORSENI'],
            ['nama_departemen' => 'Kebersihan dan Linkungan Hidup', 'singkatan' => 'DKLH'],
        ];

        $departemen = fake()->unique()->randomElement($departemens);

        return [
            'nama_departemen' => $departemen['nama_departemen'],
            'singkatan' => $departemen['singkatan'],
        ];
    }

    /**
     * Indicate that the departemen is KAMTIB.
     */
    public function kamtib(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_departemen' => 'Keamanan dan Ketertiban',
            'singkatan' => 'KAMTIB',
        ]);
    }

    /**
     * Indicate that the departemen is MADAL.
     */
    public function madal(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_departemen' => 'Madrasah Diniyah',
            'singkatan' => 'MADAL',
        ]);
    }

    /**
     * Indicate that the departemen is PORSENI.
     */
    public function porseni(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama_departemen' => 'Olahraga dan Kesenian',
            'singkatan' => 'PORSENI',
        ]);
    }
}
