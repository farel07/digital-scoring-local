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
        Schema::create('validation_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('validation_request_id')->constrained('validation_requests')->onDelete('cascade');
            $table->foreignId('juri_id')->constrained('users')->onDelete('cascade');
            $table->string('vote', 20); // 'SAH', 'TIDAK SAH', 'NETRAL'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_votes');
    }
};
