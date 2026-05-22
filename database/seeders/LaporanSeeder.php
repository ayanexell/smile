<?php

namespace Database\Seeders;

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        // Create laporan for each user
        foreach ($users as $user) {
            // Each user has 1-5 laporans
            $count = rand(1, 5);

            Laporan::factory()
                ->count($count)
                ->ownedBy($user)
                ->create();
        }

        // Create pending laporans
        Laporan::factory()
            ->count(10)
            ->pending()
            ->create();

        // Create approved laporans
        Laporan::factory()
            ->count(15)
            ->disetujui()
            ->create();
    }
}
