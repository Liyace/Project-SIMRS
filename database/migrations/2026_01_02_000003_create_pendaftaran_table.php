<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id('id_pendaftaran');
            $table->string('no_pendaftaran')->unique()->nullable(); // REG-20261120-001
            $table->foreignId('id_pasien')->constrained('pasien', 'id_pasien')->onDelete('cascade');
            $table->foreignId('id_poli')->nullable()->constrained('poli', 'id_poli')->onDelete('set null');
            $table->foreignId('id_dokter')->nullable()->constrained('pegawai', 'id_pegawai')->onDelete('set null');
            $table->string('no_antrian')->nullable(); // UMU-001, GIG-002
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->string('cara_bayar')->default('umum'); // umum, bpjs, asuransi
            $table->string('status_antrian')->default('menunggu_skrining'); 
            // menunggu_skrining, menunggu_dokter, sedang_diperiksa, menunggu_kasir, menunggu_obat, selesai, dibatalkan
            $table->text('keluhan_awal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
