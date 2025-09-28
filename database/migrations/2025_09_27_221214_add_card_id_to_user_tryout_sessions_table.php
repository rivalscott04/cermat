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
        Schema::table('user_tryout_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('card_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tryout_sessions', function (Blueprint $table) {
            $table->dropColumn('card_id');
        });
    }
};
