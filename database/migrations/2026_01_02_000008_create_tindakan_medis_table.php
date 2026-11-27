<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindakan_medis', function (Blueprint $table) {
            $table->id('id_tindakan_medis');
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');
            $table->foreignId('id_layanan')->constrained('layanan', 'id_layanan')->onDelete('restrict');
            $table->integer('qty')->default(1);
            $table->decimal('tarif_saat_ini', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindakan_medis');
    }
};
