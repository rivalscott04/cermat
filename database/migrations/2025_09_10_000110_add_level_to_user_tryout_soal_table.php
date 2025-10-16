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
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            if (!Schema::hasColumn('user_tryout_soal', 'level')) {
                $table->enum('level', ['dasar', 'mudah', 'sedang', 'sulit', 'tersulit', 'ekstrem'])->nullable()->after('soal_id');
            }
            $table->index(['tryout_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            if (Schema::hasColumn('user_tryout_soal', 'level')) {
                $table->dropColumn('level');
            }
            $table->dropIndex(['user_tryout_soal_tryout_id_level_index']);
        });
    }
};


