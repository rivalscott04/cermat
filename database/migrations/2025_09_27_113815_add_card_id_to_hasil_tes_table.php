<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->unsignedBigInteger('card_id')->nullable()->after('user_id');

            // Jika ingin ada relasi ke tabel tryouts (opsional):
            // $table->foreign('card_id')->references('id')->on('tryouts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            // Kalau tadi pakai foreign key, hapus dulu:
            // $table->dropForeign(['card_id']);
            $table->dropColumn('card_id');
        });
    }
};
