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
        // Update existing data first - map old levels to new levels
        DB::statement("UPDATE user_tryout_soal SET level = 'dasar' WHERE level = 'mudah'");
        
        // Drop the old enum and create new one
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->dropColumn('level');
        });
        
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->enum('level', ['dasar', 'mudah', 'sedang', 'sulit', 'tersulit', 'ekstrem'])->nullable()->after('soal_id');
            $table->index(['tryout_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Map new levels back to old levels
        DB::statement("UPDATE user_tryout_soal SET level = 'mudah' WHERE level = 'dasar'");
        DB::statement("UPDATE user_tryout_soal SET level = 'mudah' WHERE level = 'mudah'");
        DB::statement("UPDATE user_tryout_soal SET level = 'sedang' WHERE level = 'sedang'");
        DB::statement("UPDATE user_tryout_soal SET level = 'sulit' WHERE level = 'sulit'");
        DB::statement("UPDATE user_tryout_soal SET level = 'sulit' WHERE level = 'tersulit'");
        DB::statement("UPDATE user_tryout_soal SET level = 'sulit' WHERE level = 'ekstrem'");
        
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->dropColumn('level');
        });
        
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->enum('level', ['mudah', 'sedang', 'sulit'])->nullable()->after('soal_id');
            $table->index(['tryout_id', 'level']);
        });
    }
};