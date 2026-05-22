<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $inventarisBorrowable = Inventaris::where('dpt_dipinjam', true)
            ->where('kondisi', 'baik')
            ->get();

        // Create peminjaman for each user
        foreach ($users as $user) {
            // Each user has 1-4 peminjamans
            $count = rand(1, 4);

            for ($i = 0; $i < $count; $i++) {
                if ($inventarisBorrowable->isNotEmpty()) {
                    $inventaris = $inventarisBorrowable->random();

                    Peminjaman::factory()
                        ->oleh($user)
                        ->untuk($inventaris)
                        ->create();
                }
            }
        }

        // Create active peminjamans
        Peminjaman::factory()
            ->count(10)
            ->dipinjam()
            ->create();

        // Create late peminjamans
        Peminjaman::factory()
            ->count(5)
            ->terlambat()
            ->create();

        // Create returned peminjamans
        Peminjaman::factory()
            ->count(15)
            ->dikembalikan()
            ->create();
    }
}
