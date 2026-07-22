<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventaris;
use App\Models\Peminjaman;
use App\Models\User;
class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('nama_role', 'User');
        })->get();
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
        // Peminjaman::factory()
        //     ->count(10)
        //     ->dipinjam()
        //     ->create();

        // Create late peminjamans
        // Peminjaman::fact


        // Create returned peminjamans
        // Peminjaman::factory()
        //     ->count(15)
        //     ->dikembalikan()
        //     ->create();
    }
}
