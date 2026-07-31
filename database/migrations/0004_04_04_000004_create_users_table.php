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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('departemen_id')->nullable();
            $table->string('nama_lengkap');
            $table->string('avatar')->nullable();
            $table->char('nik', 16)->unique();
            $table->string('ktp_path')->nullable();
            $table->date('tgl_lahir');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->string('email')->unique();
            $table->string('no_wa')->unique();
            $table->text('alamat');
            $table->string('pekerjaan');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('profile_status');
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('role_id')->references('id_role')->on('roles');
            $table->foreign('departemen_id')->references('id_departemen')->on('departemens')->onDelete('set null');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
