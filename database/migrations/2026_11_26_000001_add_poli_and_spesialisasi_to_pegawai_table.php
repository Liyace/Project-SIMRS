<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (!Schema::hasColumn('pegawai', 'id_poli')) {
                $table->foreignId('id_poli')->nullable()->after('role')->constrained('poli', 'id_poli')->nullOnDelete();
            }
            if (!Schema::hasColumn('pegawai', 'spesialisasi')) {
                $table->string('spesialisasi', 100)->nullable()->after('id_poli');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (Schema::hasColumn('pegawai', 'id_poli')) {
                $table->dropForeign(['id_poli']);
                $table->dropColumn('id_poli');
            }
            if (Schema::hasColumn('pegawai', 'spesialisasi')) {
                $table->dropColumn('spesialisasi');
            }
        });
    }
};
