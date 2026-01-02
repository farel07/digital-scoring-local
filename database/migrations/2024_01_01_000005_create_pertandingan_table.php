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
        Schema::create('pertandingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('arena_id')->constrained('arena')->onDelete('cascade');
            $table->foreignId('next_match_id')->nullable()->constrained('pertandingan')->onDelete('set null');
            $table->enum('status', ['ada', 'berlangsung', 'belum_dimulai', 'selesai'])->default('belum_dimulai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertandingan');
    }
};
