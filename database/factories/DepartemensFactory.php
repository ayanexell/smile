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
            ['nama_departemen' => 'Keamanan dan Ketertiban', 'singkatan' => 'KAMTIB', 'deskripsi' => 'Departemen yang bertanggung jawab atas keamanan dan ketertiban di lingkungan organisasi.'],
            ['nama_departemen' => 'Madrasah Diniyah', 'singkatan' => 'MADAL', 'deskripsi' => 'Departemen yang mengelola kegiatan pendidikan agama dan madrasah diniyah.'],
            ['nama_departemen' => 'Peribadatan dan SKIA', 'singkatan' => 'TASKIA', 'deskripsi' => 'Departemen yang mengelola kegiatan peribadatan dan SKIA.'],
            ['nama_departemen' => 'Pengajian Al-Qur\'an & Kitab', 'singkatan' => 'DEPAK', 'deskripsi' => 'Departemen yang mengelola kegiatan Pengajian Al-Qur\'an & Kitab.'],
            ['nama_departemen' => 'Olahraga dan Kesenian', 'singkatan' => 'PORSENI', 'deskripsi' => 'Departemen yang mengelola kegiatan Olahraga dan Kesenian.'],
            ['nama_departemen' => 'Kebersihan dan Linkungan Hidup', 'singkatan' => 'DKLH', 'deskripsi' => 'Departemen yang mengelola kegiatan Kebersihan dan Lingkungan Hidup.'],
        ];

        $departemen = fake()->unique()->randomElement($departemens);

        return [
            'nama_departemen' => $departemen['nama_departemen'],
            'singkatan' => $departemen['singkatan'],
            'deskripsi' => $departemen['deskripsi'],
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
