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
            ['nama_departemen' => 'Keamanan dan Ketertiban', 'singkatan' => 'KAMTIB', 'deskripsi' => 'Departemen yang bertanggung jawab atas keamanan dan ketertiban di lingkungan organisasi.'],
            ['nama_departemen' => 'Madrasah Diniyah', 'singkatan' => 'MADAL', 'deskripsi' => 'Departemen yang mengelola kegiatan pendidikan agama dan madrasah diniyah.'],
            ['nama_departemen' => 'Peribadatan dan SKIA', 'singkatan' => 'TASKIA', 'deskripsi' => 'Departemen yang mengelola kegiatan peribadatan dan SKIA.'],
            ['nama_departemen' => 'Pengajian Al-Qur\'an & Kitab', 'singkatan' => 'DEPAK', 'deskripsi' => 'Departemen yang mengelola kegiatan Pengajian Al-Qur\'an & Kitab.'],
            ['nama_departemen' => 'Olahraga dan Kesehatan', 'singkatan' => 'DOK', 'deskripsi' => 'Departemen yang mengelola kegiatan Olahraga dan Kesehatan.'],
            ['nama_departemen' => 'Kebersihan dan Lingkungan Hidup', 'singkatan' => 'DKLH', 'deskripsi' => 'Departemen yang mengelola kegiatan Kebersihan dan Lingkungan Hidup.'],
            ['nama_departemen' => 'Bimbingan dan Konseling', 'singkatan' => 'BK', 'deskripsi' => 'Departemen yang mengelola kegiatan Bimbingan dan Konseling.'],
            ['nama_departemen' => 'Pekerjaan Umum', 'singkatan' => 'PU', 'deskripsi' => 'Departemen yang mengelola kegiatan Pekerjaan Umum.'],
            ['nama_departemen' => 'Publikasi Organisasi dan Seni', 'singkatan' => 'PORSENI', 'deskripsi' => 'Departemen yang mengelola kegiatan publikasi organisasi dan seni.'],
        ];

        foreach ($departemens as $departemen) {
            Departemens::create($departemen);
        }
    }
}
