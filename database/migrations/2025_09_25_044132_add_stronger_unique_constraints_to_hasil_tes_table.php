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
            // Add a generated column to extract session_id from JSON
            $table->string('session_id_extracted')->nullable()->after('detail_jawaban');
        });
        
        // Create the generated column using raw SQL
        DB::statement("ALTER TABLE hasil_tes MODIFY COLUMN session_id_extracted VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(detail_jawaban, '$.session_id'))) STORED");
        
        Schema::table('hasil_tes', function (Blueprint $table) {
            // Add unique constraint using the generated column
            $table->unique(['user_id', 'jenis_tes', 'session_id_extracted'], 'unique_user_test_session');
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
            
            // Drop the generated column
            $table->dropColumn('session_id_extracted');
            
            // Restore the old unique constraint
            $table->unique(['user_id', 'jenis_tes', 'tanggal_tes'], 'unique_user_test_date');
        });
    }
};