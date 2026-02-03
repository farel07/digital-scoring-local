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
        Schema::create('tanding_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanding_match_id')->constrained('tanding_matches')->onDelete('cascade');
            $table->enum('team', ['blue', 'red']);
            $table->enum('penalty_type', ['jatuhan', 'bina', 'teguran', 'peringatan']);
            $table->integer('penalty_value')->comment('Sequential value: 1, 2, 3...');
            $table->integer('point_deduction')->comment('Actual points: +3, -1, -2, -5, -10, -15');
            $table->integer('round')->default(1);
            $table->boolean('caused_disqualification')->default(false);
            $table->timestamps();

            $table->index(['tanding_match_id', 'team', 'penalty_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanding_penalties');
    }
};
