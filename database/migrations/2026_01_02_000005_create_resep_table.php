<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep', function (Blueprint $table) {
            $table->id('id_resep');
            $table->string('no_resep')->unique()->nullable(); // RSP-20261120-001
            $table->foreignId('id_pendaftaran')->constrained('pendaftaran', 'id_pendaftaran')->onDelete('cascade');
            $table->foreignId('id_dokter')->nullable()->constrained('pegawai', 'id_pegawai')->onDelete('set null');
            $table->foreignId('id_apoteker')->nullable()->constrained('pegawai', 'id_pegawai')->onDelete('set null');
            $table->decimal('total_harga_obat', 12, 2)->default(0);
            $table->string('status_resep')->default('draft'); 
            // draft, menunggu_pembayaran, siap_racik, siap_diambil, sudah_diserahkan
            $table->text('catatan_dokter')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
