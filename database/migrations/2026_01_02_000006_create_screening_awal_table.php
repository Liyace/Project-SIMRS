<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_awal', function (Blueprint $table) {
            $table->id('id_screening');
            $table->foreignId('id_pendaftaran')->unique()->constrained('pendaftaran', 'id_pendaftaran')->onDelete('cascade');
            $table->foreignId('id_perawat')->nullable()->constrained('pegawai', 'id_pegawai')->onDelete('set null');
            $table->integer('sistol')->nullable()->comment('mmHg');
            $table->integer('diastol')->nullable()->comment('mmHg');
            $table->decimal('suhu_tubuh', 4, 1)->nullable()->comment('Celcius');
            $table->decimal('berat_badan', 5, 2)->nullable()->comment('kg');
            $table->decimal('tinggi_badan', 5, 2)->nullable()->comment('cm');
            $table->integer('denyut_nadi')->nullable()->comment('bpm');
            $table->text('catatan_perawat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screening_awal');
    }
};
