<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Laporan>
 */
class LaporanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $status = ['pending', 'disetujui', 'ditolak'];

        return [
            'user_id' => User::factory(),
            'judul_laporan' => 'Laporan ' . fake()->sentence(3),
            'month' => fake()->randomElement($bulan) . ' ' . fake()->year(),
            'file_path' => 'laporans/' . fake()->uuid() . '.pdf',
            'status' => fake()->randomElement($status),
        ];
    }

    /**
     * Set laporan with pending status.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Set laporan with disetujui status.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disetujui',
        ]);
    }

    /**
     * Set laporan with ditolak status.
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditolak',
        ]);
    }

    /**
     * Set laporan for specific month.
     */
    public function forMonth(string $month): static
    {
        return $this->state(fn (array $attributes) => [
            'month' => $month,
        ]);
    }

    /**
     * Set laporan with specific user.
     */
    public function ownedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id_user,
        ]);
    }
}
