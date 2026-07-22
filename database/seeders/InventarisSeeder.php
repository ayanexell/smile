<?php

namespace Database\Seeders;

use App\Models\Inventaris;
use App\Models\Departemens;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemens = Departemens::all();

        // Create inventaris for each departemen
        foreach ($departemens as $departemen) {
            // Each departemen has 3-7 inventaris items
            $count = rand(3, 7);

            Inventaris::factory()
                ->count($count)
                ->ownedBy($departemen)
                ->create();
        }

    }
}
