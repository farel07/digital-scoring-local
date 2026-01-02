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
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertandingan_id')->constrained('pertandingan')->onDelete('cascade');
            $table->string('penalty_id')->comment('Unique identifier untuk penalty spesifik');
            $table->string('type')->comment('Tipe penalty: WAKTU, KELUAR_GARIS, SENJATA_JATUH, dll');
            $table->decimal('value', 5, 2)->comment('Nilai penalty (biasanya -0.50)');
            $table->enum('status', ['active', 'cleared'])->default('active');
            $table->timestamps();

            // Index untuk query cepat
            $table->index(['pertandingan_id', 'status']);
            $table->unique('penalty_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
