<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Peminjaman;
/**
 * @extends Factory Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    protected $model = Peminjaman::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tgl_peminjaman = Carbon::instance(fake()->dateTimeBetween('-2 months', 'now'));
        $tgl_pengembalian = Carbon::instance(fake()->dateTimeBetween($tgl_peminjaman, '+1 month'));
        return [
            'user_id' => User::factory(),
            'inventaris_id' => Inventaris::factory()->dapatDipinjam(),
            'tgl_peminjaman' => $tgl_peminjaman,
            'tgl_pengembalian' => $tgl_pengembalian,
            'status' => fake()->randomElement(['dipinjam', 'dikembalikan', 'terlambat']),
            'jumlah' => fake()->numberBetween(1, 5),
            'hibah' => fake()->numberBetween(0, 2),
            'lambat' => false,
        ];
    }

    /**
     * Peminjaman yang terjadi dalam 7 hari terakhir.
     */
    public function dalam7HariTerakhir(): static
    {
        return $this->state(function () {
            $now = Carbon::now();
            return [
                'tgl_peminjaman' => Carbon::instance(
                    fake()->dateTimeBetween($now->copy()->subDays(7), $now)
                ),
            ];
        });
    }

    /**
     * Peminjaman yang masih berlangsung (belum dikembalikan sampai sekarang).
     */
    public function masihDipinjam(): static
    {
        return $this->state(function () {
            $now = Carbon::now();
            return [
                'status' => 'dipinjam',
                'tgl_pengembalian' => Carbon::instance(
                    fake()->dateTimeBetween($now->copy()->addDay(), '+2 weeks')
                ),
                'lambat' => false,
            ];
        });
    }

    /**
     * Peminjaman dengan tanggal pinjam spesifik.
     */
    public function denganTanggalPinjam(Carbon $date): static
    {
        return $this->state(fn() => ['tgl_peminjaman' => $date]);
    }

    /**
     * Peminjaman dengan tanggal kembali spesifik.
     */
    public function denganTanggalKembali(Carbon $date): static
    {
        return $this->state(fn() => ['tgl_pengembalian' => $date]);
    }

    /**
     * Peminjaman dengan jumlah tertentu.
     */
    public function denganJumlah(int $jumlah): static
    {
        return $this->state(fn() => ['jumlah' => $jumlah]);
    }

    /**
     * Set peminjaman status as dipinjam.
     */
    public function dipinjam(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'dipinjam',
            'lambat' => false,
        ]);
    }

    /**
     * Set peminjaman status as dikembalikan.
     */
    public function dikembalikan(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'dikembalikan',
        ]);
    }

    /**
     * Set peminjaman status as terlambat.
     */
    public function terlambat(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'terlambat',
            'lambat' => true,
        ]);
    }

    /**
     * Set peminjaman with late return.
     */
    public function lambat(): static
    {
        return $this->state(fn(array $attributes) => [
            'lambat' => true,
            'status' => 'terlambat',
        ]);
    }

    /**
     * Set peminjaman without late return.
     */
    public function tepatWaktu(): static
    {
        return $this->state(fn(array $attributes) => [
            'lambat' => false,
        ]);
    }

    /**
     * Set peminjaman with specific user.
     */
    public function oleh(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id_user,
        ]);
    }

    /**
     * Set peminjaman for specific inventaris.
     */
    public function untuk(Inventaris $inventaris): static
    {
        return $this->state(fn(array $attributes) => [
            'inventaris_id' => $inventaris->id_inventaris,
        ]);
    }

    /**
     * Set peminjaman with specific hibah amount.
     */
    public function denganHibah(int $amount): static
    {
        return $this->state(fn(array $attributes) => [
            'hibah' => $amount,
        ]);
    }
}
