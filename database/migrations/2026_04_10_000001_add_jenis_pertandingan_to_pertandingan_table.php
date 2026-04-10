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
            $table->enum('jenis_pertandingan', ['pemasalan', 'prestasi'])
                ->default('prestasi')
                ->after('arena_id')
                ->comment('Jenis pertandingan: pemasalan atau prestasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pertandingan', function (Blueprint $table) {
            $table->dropColumn('jenis_pertandingan');
        });
    }
};
