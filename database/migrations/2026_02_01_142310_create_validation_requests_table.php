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
        Schema::create('validation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanding_match_id')->constrained('tanding_matches')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->string('validation_type', 50); // 'jatuhan', 'pelanggaran'
            $table->string('team', 10); // 'blue' or 'red'
            $table->text('description')->nullable();
            $table->string('result', 20)->nullable(); // 'SAH', 'TIDAK SAH', 'NETRAL', 'INVALID'
            $table->string('status', 20)->default('pending'); // 'pending', 'completed'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_requests');
    }
};
