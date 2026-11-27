<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id('id_rekam_medis');
            $table->foreignId('id_pendaftaran')->unique()->constrained('pendaftaran', 'id_pendaftaran')->onDelete('cascade');
            $table->foreignId('id_dokter')->nullable()->constrained('pegawai', 'id_pegawai')->onDelete('set null');
            $table->text('anamnesis')->nullable(); // Keluhan subjektif
            $table->string('diagnosa_utama');
            $table->string('kode_icd10')->nullable();
            $table->text('diagnosa_sekunder')->nullable();
            $table->text('terapi_tindakan')->nullable();
            $table->text('catatan_edukasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
