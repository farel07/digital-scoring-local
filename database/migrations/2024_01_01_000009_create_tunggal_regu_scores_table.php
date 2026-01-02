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
        Schema::create('tunggal_regu_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertandingan_id')->constrained('pertandingan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('total_errors')->default(0)->comment('Total kesalahan dari semua jurus');
            $table->decimal('correctness_score', 5, 2)->default(9.90)->comment('9.90 - (total_errors × 0.01)');
            $table->decimal('category_score', 4, 2)->default(0.00)->comment('Kemantapan/Penghayatan/Stamina (0.01-0.10)');
            $table->decimal('total_score', 5, 2)->default(9.90)->comment('correctness_score + category_score');
            $table->json('errors_per_jurus')->nullable()->comment('JSON: {1: 0, 2: 2, 3: 1, ...}');
            $table->timestamps();

            // Unique constraint: satu user (juri) hanya bisa submit satu kali per pertandingan
            $table->unique(['pertandingan_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunggal_regu_scores');
    }
};
