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
        Schema::create('pertandingan_player', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertandingan_id')->constrained('pertandingan')->onDelete('cascade');
            $table->string('player_name');
            $table->string('player_contingent');
            $table->integer('side_number'); // 1 = blue, 2 = red
            $table->decimal('total_score', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertandingan_player');
    }
};
