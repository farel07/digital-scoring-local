<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix existing records that have string-encoded JSON instead of proper JSON
        DB::statement("
            UPDATE tunggal_regu_scores 
            SET errors_per_jurus = '[]' 
            WHERE errors_per_jurus IS NULL 
               OR errors_per_jurus = '\"[]\"'
               OR errors_per_jurus = '\"{}\"'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for data fix
    }
};
