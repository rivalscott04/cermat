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
        Schema::create('tryout_blueprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tryout_id')->constrained()->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori_soal')->onDelete('cascade');
            $table->enum('level', ['mudah', 'sedang', 'sulit']);
            $table->unsignedInteger('jumlah');
            $table->timestamps();

            $table->unique(['tryout_id', 'kategori_id', 'level']);
            $table->index(['kategori_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_blueprints');
    }
};


