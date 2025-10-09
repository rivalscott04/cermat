<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kategori_soal', function (Blueprint $table) {
            $table->string('scoring_mode', 16)->nullable()->after('deskripsi');
            $table->index('scoring_mode');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_soal', function (Blueprint $table) {
            $table->dropIndex(['scoring_mode']);
            $table->dropColumn('scoring_mode');
        });
    }
};


