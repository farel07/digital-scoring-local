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
        Schema::create('penalty_rules', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // waktu, keluar_garis, etc.
            $table->string('name');
            $table->decimal('value', 4, 2); // e.g., -0.50
            $table->string('category')->default('tunggal'); // tunggal, ganda, regu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalty_rules');
    }
};
