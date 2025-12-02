<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Pastikan kolom tetap nullable saat diubah ke longText
            $table->longText('payment_details')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Kembalikan ke text, tetap nullable
            $table->text('payment_details')->nullable()->change();
        });
    }
};
