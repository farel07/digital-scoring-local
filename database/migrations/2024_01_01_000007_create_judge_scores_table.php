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
        Schema::create('judge_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertandingan_id')->constrained('pertandingan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('teknik', 4, 2)->default(0)->comment('Skor teknik dasar (0.00-0.30)');
            $table->decimal('kekuatan', 4, 2)->default(0)->comment('Skor kekuatan & kecepatan (0.00-0.30)');
            $table->decimal('penampilan', 4, 2)->default(0)->comment('Skor penampilan & gaya (0.00-0.30)');
            $table->decimal('total', 5, 2)->default(9.10)->comment('Total skor (9.10 + teknik + kekuatan + penampilan)');
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
        Schema::dropIfExists('judge_scores');
    }
};
