<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Roles;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nama_role' => 'Super Admin'],
            ['nama_role' => 'Admin'],
            ['nama_role' => 'Koordinator'],
            ['nama_role' => 'User'],
        ];

        foreach ($roles as $role) {
            Roles::firstOrCreate(
                ['nama_role' => $role['nama_role']],
                $role
            );
        }
    }
}
