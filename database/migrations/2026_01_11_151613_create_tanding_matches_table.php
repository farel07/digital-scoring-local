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
        Schema::create('tanding_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertandingan_id')->constrained('pertandingan')->onDelete('cascade');
            $table->integer('current_round')->default(1)->comment('Round 1, 2, or 3');
            $table->integer('blue_total_score')->default(0)->comment('Can be negative');
            $table->integer('red_total_score')->default(0)->comment('Can be negative');
            $table->boolean('blue_disqualified')->default(false);
            $table->boolean('red_disqualified')->default(false);
            $table->enum('match_status', ['not_started', 'in_progress', 'finished'])->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            // One tanding match per pertandingan
            $table->unique('pertandingan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanding_matches');
    }
};
