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
        Schema::table('soals', function (Blueprint $table) {
            if (!Schema::hasColumn('soals', 'level')) {
                $table->enum('level', ['mudah', 'sedang', 'sulit'])->default('mudah')->after('tipe');
            }
            $table->index(['kategori_id', 'level', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            if (Schema::hasColumn('soals', 'level')) {
                $table->dropColumn('level');
            }
            $table->dropIndex(['soals_kategori_id_level_is_active_index']);
        });
    }
};


