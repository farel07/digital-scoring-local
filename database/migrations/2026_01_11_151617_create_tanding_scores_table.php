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
        Schema::create('tanding_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanding_match_id')->constrained('tanding_matches')->onDelete('cascade');
            $table->foreignId('judge_id')->constrained('users')->onDelete('cascade')->comment('Juri who gave the score');
            $table->enum('team', ['blue', 'red']);
            $table->enum('technique', ['pukul', 'tendang'])->comment('pukul=+1, tendang=+2');
            $table->integer('round')->default(1);
            $table->integer('points')->comment('1 for pukul, 2 for tendang');
            $table->enum('status', ['input', 'sah', 'cancelled'])->default('input');
            $table->timestamps();

            $table->index(['tanding_match_id', 'team', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanding_scores');
    }
};
