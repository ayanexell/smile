<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_peminjamen', function (Blueprint $table) {
            $table->id('id_laporan_peminjaman');
            $table->string('laporan_path');
            $table->string('tahun');
            $table->integer('total_hibah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_peminjamen');
    }
};
