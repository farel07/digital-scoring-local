<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists before adding
        if (!Schema::hasColumn('tunggal_regu_scores', 'side')) {
            Schema::table('tunggal_regu_scores', function (Blueprint $table) {
                $table->enum('side', ['1', '2'])->nullable()->after('user_id')->comment('Side 1 (Blue) or Side 2 (Red)');
            });

            // Set default value for existing records
            DB::table('tunggal_regu_scores')->whereNull('side')->update(['side' => '1']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tunggal_regu_scores', 'side')) {
            Schema::table('tunggal_regu_scores', function (Blueprint $table) {
                $table->dropColumn('side');
            });
        }
    }
};
