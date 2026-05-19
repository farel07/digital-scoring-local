<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pertandingan', function (Blueprint $table) {
            // Nomor partai per-kelas (nullable karena akan diisi via CSV import)
            // Kombinasi kelas_id + nomor_partai harus unik (kecuali jika null)
            $table->unsignedInteger('nomor_partai')->nullable()->after('arena_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertandingan', function (Blueprint $table) {
            $table->dropColumn('nomor_partai');
        });
    }
};
