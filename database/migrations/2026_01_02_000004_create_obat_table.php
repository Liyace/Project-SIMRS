<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->id('id_obat');
            $table->string('kode_obat')->unique()->nullable();
            $table->string('nama_obat');
            $table->string('satuan')->default('tablet'); // tablet, botol, kapsul, strip, tube
            $table->decimal('harga_beli', 12, 2)->default(0);
            $table->decimal('harga_obat', 12, 2)->default(0); // harga jual ke pasien
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
