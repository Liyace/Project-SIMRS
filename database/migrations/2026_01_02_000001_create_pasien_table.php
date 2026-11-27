<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id('id_pasien');
            $table->string('rm')->unique(); // No. Rekam Medis (e.g. RM-0001)
            $table->string('nik')->unique();
            $table->string('nama_pasien');
            $table->string('password_hash')->nullable(); // Untuk login akun mandiri (Mobile App)
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
