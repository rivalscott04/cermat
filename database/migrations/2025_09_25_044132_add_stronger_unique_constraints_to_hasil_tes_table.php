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
        // Check if the old unique constraint exists and drop it safely
        try {
            DB::statement('ALTER TABLE hasil_tes DROP INDEX unique_user_test_date');
        } catch (\Exception $e) {
            // Index doesn't exist, continue
        }
        
        Schema::table('hasil_tes', function (Blueprint $table) {
            // Add stronger unique constraint based on user, test type, and session_id in detail_jawaban
            // This prevents duplicates when viewing test details multiple times
            $table->unique(['user_id', 'jenis_tes', 'detail_jawaban'], 'unique_user_test_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('unique_user_test_session');
            
            // Restore the old unique constraint
            $table->unique(['user_id', 'jenis_tes', 'tanggal_tes'], 'unique_user_test_date');
        });
    }
};