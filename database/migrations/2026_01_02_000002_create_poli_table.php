<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poli', function (Blueprint $table) {
            $table->id('id_poli');
            $table->string('kode_poli')->unique()->nullable(); // UMU, GIG, ANK, INT, MAT
            $table->string('nama_poli');
            $table->decimal('biaya_konsultasi', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poli');
    }
};
