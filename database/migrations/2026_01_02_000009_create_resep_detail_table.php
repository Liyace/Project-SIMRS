<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_detail', function (Blueprint $table) {
            $table->id('id_resep_detail');
            $table->foreignId('id_resep')->constrained('resep', 'id_resep')->onDelete('cascade');
            $table->foreignId('id_obat')->constrained('obat', 'id_obat')->onDelete('restrict');
            $table->integer('jumlah')->default(1);
            $table->string('aturan_pakai')->default('3x1 sesudah makan');
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_detail');
    }
};
