<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departemens;
class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemens = [
            ['nama_departemen' => 'Keamanan dan Ketertiban', 'singkatan' => 'KAMTIB'],
            ['nama_departemen' => 'Madrasah Diniyah', 'singkatan' => 'MADAL'],
            ['nama_departemen' => 'Peribadatan dan SKIA', 'singkatan' => 'TASKIA'],
            ['nama_departemen' => 'Pengajian Al-Qur\'an & Kitab', 'singkatan' => 'DEPAK'],
            ['nama_departemen' => 'Olahraga dan Kesenian', 'singkatan' => 'PORSENI'],
            ['nama_departemen' => 'Kebersihan dan Linkungan Hidup', 'singkatan' => 'DKLH'],
        ];

        foreach ($departemens as $departemen) {
            Departemens::create($departemen);
        }
    }
}
