<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Departemens;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Peminjaman;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventaris>
 */
class InventarisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $barang = [
            'Laptop', 'Proyektor', 'Printer', 'Meja', 'Kursi',
            'Sound System', 'Kamera', 'Tripod', 'Whiteboard', 'Lemari',
            'Sound System Portable', 'Tenda', 'Meja Lipat', 'Kursi Lipat',
            'Kipas Angin', 'AC Portable', 'Speaker', 'Microphone', 'Gitar',
            'Keyboard', 'Drum', 'Seragam', 'Handy Talky', 'Alat Tulis'
        ];

        $warna = ['Hitam', 'Putih', 'Merah', 'Biru', 'Hijau', 'Kuning', 'Abu-abu', 'Coklat'];
        $tipe = ['Elektronik', 'Furniture', 'Alat Musik', 'Perlengkapan', 'Kendaraan'];

        $namaBarang = fake()->randomElement($barang);
        // $departemen = Departemens::factory()->create();

        return [
            // Jangan set default departemen_id, biarkan null atau set manual
            'nama_barang' => $namaBarang,
            'jumlah' => fake()->numberBetween(1, 50),
            'kondisi' => fake()->randomElement(['baik', 'rusak']),
            'tipe' => fake()->randomElement($tipe),
            'img_path' => 'inventaris/' . fake()->uuid() . '.jpg',
            'warna' => fake()->randomElement($warna),
            'dpt_dipinjam' => fake()->boolean(80),
        ];
    }

    /**
     * Set inventaris with specific departemen.
     */
    public function ownedBy(Departemens $departemen): static
    {
        return $this->state(fn (array $attributes) => [
            'departemen_id' => $departemen->id_departemen,
        ]);
    }

     /**
     * Set inventaris in good condition.
     */
    public function baik(): static
    {
        return $this->state(fn (array $attributes) => [
            'kondisi' => 'baik',
        ]);
    }

    /**
     * Set inventaris in damaged condition.
     */
    public function rusak(): static
    {
        return $this->state(fn (array $attributes) => [
            'kondisi' => 'rusak',
        ]);
    }

    /**
     * Set inventaris as borrowable.
     */
    public function dapatDipinjam(): static
    {
        return $this->state(fn (array $attributes) => [
            'dpt_dipinjam' => true,
            'kondisi' => 'baik',
        ]);
    }

    /**
     * Set inventaris as not borrowable.
     */
    public function tidakDapatDipinjam(): static
    {
        return $this->state(fn (array $attributes) => [
            'dpt_dipinjam' => false,
        ]);
    }

    /**
     * Set inventaris with specific tipe.
     */
    public function elektronik(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipe' => 'Elektronik',
        ]);
    }

    public function furniture(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipe' => 'Furniture',
        ]);
    }
}
