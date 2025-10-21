<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration fixes the unique constraint on tryout_blueprints table.
     * The correct constraint should be on (tryout_id, kategori_id, level) to allow
     * the same kategori_id to have multiple different levels in one tryout.
     */
    public function up(): void
    {
        // Get all indexes on the table
        $indexes = DB::select("SHOW INDEX FROM tryout_blueprints WHERE Key_name != 'PRIMARY'");
        
        // Drop all existing unique constraints except PRIMARY
        foreach ($indexes as $index) {
            if ($index->Non_unique == 0) { // 0 means UNIQUE constraint
                try {
                    DB::statement("ALTER TABLE tryout_blueprints DROP INDEX `{$index->Key_name}`");
                    echo "Dropped index: {$index->Key_name}\n";
                } catch (\Exception $e) {
                    echo "Could not drop index {$index->Key_name}: {$e->getMessage()}\n";
                }
            }
        }
        
        // Add the correct unique constraint
        // This allows: same tryout_id + same kategori_id + different levels
        Schema::table('tryout_blueprints', function (Blueprint $table) {
            $table->unique(['tryout_id', 'kategori_id', 'level'], 'tryout_blueprints_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tryout_blueprints', function (Blueprint $table) {
            try {
                $table->dropUnique('tryout_blueprints_unique');
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
        });
    }
};

