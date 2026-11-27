<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->unsignedBigInteger('id_pendaftaran')->nullable();
            $table->unsignedBigInteger('id_resep')->nullable();
            $table->foreignId('id_layanan')->nullable()->constrained('layanan', 'id_layanan');
            $table->foreignId('id_pegawai')->nullable()->constrained('pegawai', 'id_pegawai');
            $table->decimal('total_bayar', 12, 2)->default(0);
            $table->decimal('total_resep', 12, 2)->default(0);
            $table->decimal('jasa_perawat', 12, 2)->default(0);
            $table->string('status_pembayaran')->default('belum_bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
