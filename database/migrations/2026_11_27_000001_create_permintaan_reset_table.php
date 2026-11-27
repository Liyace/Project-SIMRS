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
        Schema::create('permintaan_reset', function (Blueprint $table) {
            $table->id('id_permintaan');
            $table->enum('tipe_pengguna', ['pegawai', 'pasien'])->default('pegawai');
            $table->unsignedBigInteger('id_pengguna')->nullable();
            $table->string('nik', 30)->index();
            $table->string('nama', 100);
            $table->string('no_hp', 30)->nullable();
            $table->enum('tipe_permintaan', ['password', '2fa', 'keduanya'])->default('password');
            $table->text('catatan')->nullable();
            $table->enum('status', ['menunggu', 'selesai', 'ditolak'])->default('menunggu');
            $table->string('password_baru_plain', 100)->nullable();
            $table->text('catatan_admin')->nullable();
            $table->unsignedBigInteger('diproses_oleh')->nullable();
            $table->timestamp('diproses_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_reset');
    }
};
