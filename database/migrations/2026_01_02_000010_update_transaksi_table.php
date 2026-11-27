<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi', 'no_invoice')) {
                $table->string('no_invoice')->unique()->nullable()->after('id_transaksi');
            }
            if (!Schema::hasColumn('transaksi', 'total_layanan')) {
                $table->decimal('total_layanan', 12, 2)->default(0)->after('id_pegawai');
            }
            if (!Schema::hasColumn('transaksi', 'jumlah_dibayar')) {
                $table->decimal('jumlah_dibayar', 12, 2)->default(0)->after('total_bayar');
            }
            if (!Schema::hasColumn('transaksi', 'kembalian')) {
                $table->decimal('kembalian', 12, 2)->default(0)->after('jumlah_dibayar');
            }
            if (!Schema::hasColumn('transaksi', 'metode_bayar')) {
                $table->string('metode_bayar')->default('tunai')->after('kembalian');
            }
            if (!Schema::hasColumn('transaksi', 'waktu_pembayaran')) {
                $table->timestamp('waktu_pembayaran')->nullable()->after('status_pembayaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['no_invoice', 'total_layanan', 'jumlah_dibayar', 'kembalian', 'metode_bayar', 'waktu_pembayaran']);
        });
    }
};
