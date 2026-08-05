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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('id_peminjaman');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('inventaris_id');
            $table->datetime('tgl_peminjaman');
            $table->datetime('tgl_pengembalian');
            $table->integer('jumlah');
            $table->string('status');
            $table->integer('hibah')->nullable();
            $table->boolean('lambat')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id_user')->on('users');
            $table->foreign('inventaris_id')->references('id_inventaris')->on('inventaris');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
