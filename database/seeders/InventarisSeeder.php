<?php

namespace Database\Seeders;

use App\Models\Inventaris;
use App\Models\Departemens;
use App\Models\User;
use Illuminate\Database\Seeder;

use App\Models\Roles;
class InventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::onlyKoordinators()->get();

        // Create inventaris for each user
        foreach ($users as $user) {
            // Each user has 3-7 inventaris items
            $count = rand(3, 7);

            Inventaris::factory()
                ->count($count)
                ->ownedBy($user)
                ->create();
        }

    }
}
