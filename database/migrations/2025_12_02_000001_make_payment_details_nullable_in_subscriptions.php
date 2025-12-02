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
        // Safety: hanya ubah kalau kolomnya ada
        if (Schema::hasColumn('subscriptions', 'payment_details')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->longText('payment_details')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Balikin ke non-nullable kalau kolomnya ada
        if (Schema::hasColumn('subscriptions', 'payment_details')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->longText('payment_details')->nullable(false)->change();
            });
        }
    }
};


