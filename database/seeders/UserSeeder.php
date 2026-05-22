<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Roles;
use App\Models\Departemens;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Roles::where('nama_role', 'Super Admin')->first();
        $adminRole = Roles::where('nama_role', 'Admin')->first();
        $userRole = Roles::where('nama_role', 'User')->first();

        $kamtib = Departemens::where('singkatan', 'KAMTIB')->first();
        $madal = Departemens::where('singkatan', 'MADAL')->first();
        $porseni = Departemens::where('singkatan', 'PORSENI')->first();

        // Create Super Admin
        User::factory()->create([
            'role_id' => $superAdminRole->id_role,
            'departemen_id' => null,
            'nama_lengkap' => 'Super Admin User',
            'nik' => '3201010101900001',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create Admins
        User::factory()->admin()->count(3)->create([
            'departemen_id' => $kamtib->id_departemen,
        ]);

        // Create Regular Users
        User::factory()->regularUser()->count(10)->create();

        // Create Users with specific departemens
        User::factory()->regularUser()->count(5)->create([
            'departemen_id' => $madal->id_departemen,
        ]);

        User::factory()->regularUser()->count(5)->create([
            'departemen_id' => $porseni->id_departemen,
        ]);
    }
}
