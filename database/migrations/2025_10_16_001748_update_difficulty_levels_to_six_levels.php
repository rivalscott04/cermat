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
        // Update existing data dulu hanya kalau kolom level memang ada
        if (Schema::hasColumn('soals', 'level')) {
            DB::statement("UPDATE soals SET level = 'dasar' WHERE level = 'mudah'");

            // Drop enum lama
            Schema::table('soals', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }

        // Tambah enum level baru (kalau belum ada)
        if (!Schema::hasColumn('soals', 'level')) {
            Schema::table('soals', function (Blueprint $table) {
                $table->enum('level', ['dasar', 'mudah', 'sedang', 'sulit', 'tersulit', 'ekstrem'])
                    ->default('dasar')
                    ->after('tipe');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cuma jalanin rollback kalau kolom level memang ada
        if (Schema::hasColumn('soals', 'level')) {
            // Map new levels back to old levels
            DB::statement("UPDATE soals SET level = 'mudah' WHERE level = 'dasar'");
            DB::statement("UPDATE soals SET level = 'mudah' WHERE level = 'mudah'");
            DB::statement("UPDATE soals SET level = 'sedang' WHERE level = 'sedang'");
            DB::statement("UPDATE soals SET level = 'sulit' WHERE level = 'sulit'");
            DB::statement("UPDATE soals SET level = 'sulit' WHERE level = 'tersulit'");
            DB::statement("UPDATE soals SET level = 'sulit' WHERE level = 'ekstrem'");

            Schema::table('soals', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }

        // Tambah lagi enum lama kalau belum ada
        if (!Schema::hasColumn('soals', 'level')) {
            Schema::table('soals', function (Blueprint $table) {
                $table->enum('level', ['mudah', 'sedang', 'sulit'])
                    ->default('mudah')
                    ->after('tipe');
            });
        }
    }
};