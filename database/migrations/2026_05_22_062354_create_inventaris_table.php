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
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id('id_inventaris');
            $table->unsignedBigInteger('departemen_id');
            $table->string('nama_barang');
            $table->integer('jumlah');
            $table->enum('kondisi', ['baik', 'rusak']);
            $table->string('tipe');
            $table->string('img_path');
            $table->string('warna');
            $table->boolean('dpt_dipinjam');
            $table->timestamps();
            $table->foreign('departemen_id')->references('id_departemen')->on('departemens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
