<?php

namespace Database\Seeders;

use App\Models\Inventaris;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        // Create inventaris for each user
        foreach ($users as $user) {
            // Each user has 3-7 inventaris items
            $count = rand(3, 7);

            Inventaris::factory()
                ->count($count)
                ->ownedBy($user)
                ->create();
        }

        // Create specific items that can be borrowed
        Inventaris::factory()
            ->count(20)
            ->dapatDipinjam()
            ->create();

        // Create some damaged items
        Inventaris::factory()
            ->count(5)
            ->rusak()
            ->create();
    }
}
