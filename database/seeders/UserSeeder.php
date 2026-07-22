<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Roles;
use App\Models\Departemens;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
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
        $bk = Departemens::where('singkatan', 'BK')->first();
        $taskia = Departemens::where('singkatan', 'TASKIA')->first();
        $depak = Departemens::where('singkatan', 'DEPAK')->first();
        $dok = Departemens::where('singkatan', 'DOK')->first();
        $dklh = Departemens::where('singkatan', 'DKLH')->first();
        $porseni = Departemens::where('singkatan', 'PORSENI')->first();
        $pu = Departemens::where('singkatan', 'PU')->first();
        $bump = Departemens::where('singkatan', 'BUMP')->first();
        $dpbl = Departemens::where('singkatan', 'DPBL')->first();

        // Create Super Admin
        User::create([
            'role_id' => $superAdminRole->id_role,
            'departemen_id' => null,
            'nama_lengkap' => 'Super Admin User',
            'nik' => '3201010101900001',
            'tgl_lahir' => fake()->date('Y-m-d', '-18 years'),
            'Alamat' => fake()->address(),
            'jenis_kelamin' => 'laki-laki',
            'pekerjaan' => 'Mahasiswa',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create Admins
        User::factory()->admin()->count(3)->create();

        // Create Koordinator
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $kamtib->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $madal->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $taskia->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $depak->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $dklh->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $porseni->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $pu->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $bump->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $dpbl->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $dok->id_departemen,
        ]);
        User::factory()->koordinator()->count(1)->create([
            'departemen_id' => $bk->id_departemen,
        ]);

        // Create Regular Users
        User::factory()->regularUser()->count(10)->create();
    }
}
