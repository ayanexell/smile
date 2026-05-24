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
            ['nama_departemen' => 'Bimbingan dan Konseling', 'singkatan' => 'BK'],
            ['nama_departemen' => 'Peribadatan Takmir dan SKIA', 'singkatan' => 'TASKIA'],
            ['nama_departemen' => 'Pengajian Al-Qur\'an & Kitab', 'singkatan' => 'DEPAK'],
            ['nama_departemen' => 'Olahraga dan Kesehatan', 'singkatan' => 'DOK'],
            ['nama_departemen' => 'Kebersihan dan Lingkungan Hidup', 'singkatan' => 'DKLH'],
            ['nama_departemen' => 'Publikasi Organisasi dan Seni', 'singkatan' => 'PORSENI'],
            ['nama_departemen' => 'Pekerjaan Umum', 'singkatan' => 'PU'],
            ['nama_departemen' => 'Badan Usaha Milik Pesantren', 'singkatan' => 'BUMP'],
            ['nama_departemen' => 'Pengambangan Bahasa dan Lokal', 'singkatan' => 'DPBL'],
        ];

        foreach ($departemens as $departemen) {
            Departemens::create($departemen);
        }
    }
}
