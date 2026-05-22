<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departemens', function (Blueprint $table) {
            $table->id('id_departemen');
            $table->string('nama_departemen');
            $table->string('singkatan')->nullable();
            $table->timestamps();
        });

        // Departemen Default
        $departemens = [
            ['nama_departemen' => 'Keamanan dan Ketertiban', 'singkatan' => 'KAMTIB'],
            ['nama_departemen' => 'Madrasah Diniyah', 'singkatan' => 'MADAL'],
            ['nama_departemen' => 'Peribadatan dan SKIA', 'singkatan' => 'TASKIA'],
            ['nama_departemen' => 'Pengajian Al-Qur\'an & Kitab', 'singkatan' => 'DEPAK'],
            ['nama_departemen' => 'Olahraga dan Kesenian', 'singkatan' => 'PORSENI'],
            ['nama_departemen' => 'Kebersihan dan Linkungan Hidup', 'singkatan' => 'DKLH'],
        ];
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departemens');
    }
};
