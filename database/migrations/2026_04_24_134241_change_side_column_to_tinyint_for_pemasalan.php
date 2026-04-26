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
        // Mengubah tipe data 'side' yang sebelumnya enum('1', '2') menjadi varchar(10) untuk support pemasalan (> 2 peserta)
        DB::statement("ALTER TABLE judge_scores MODIFY COLUMN side VARCHAR(10) NULL COMMENT 'Participant Side Number'");
        DB::statement("ALTER TABLE penalties MODIFY COLUMN side VARCHAR(10) NULL COMMENT 'Participant Side Number'");
        
        if (Schema::hasTable('tunggal_regu_scores') && Schema::hasColumn('tunggal_regu_scores', 'side')) {
            DB::statement("ALTER TABLE tunggal_regu_scores MODIFY COLUMN side VARCHAR(10) NULL COMMENT 'Participant Side Number'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: rollback akan error (Data truncated) jika database sudah terlanjur memiliki nilai side = '3' dst.
        DB::statement("ALTER TABLE judge_scores MODIFY COLUMN side ENUM('1', '2') NULL");
        DB::statement("ALTER TABLE penalties MODIFY COLUMN side ENUM('1', '2') NULL");
        
        if (Schema::hasTable('tunggal_regu_scores') && Schema::hasColumn('tunggal_regu_scores', 'side')) {
             DB::statement("ALTER TABLE tunggal_regu_scores MODIFY COLUMN side ENUM('1', '2') NULL");
        }
    }
};
