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
        // Add indexes untuk optimasi performa login dan dashboard
        
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index(['email', 'role']); // Composite index untuk login
            $table->index(['is_active', 'created_at']); // Index untuk dashboard stats
            $table->index('created_at'); // Index untuk pelanggan baru
        });

        // Subscriptions table indexes
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'payment_status', 'end_date']); // Composite index untuk hasActiveSubscription
            $table->index(['payment_status', 'created_at']); // Index untuk revenue stats
            $table->index('package_type'); // Index untuk subscription analysis
        });

        // Soals table indexes
        Schema::table('soals', function (Blueprint $table) {
            $table->index(['is_active', 'kategori_id']); // Index untuk dashboard stats
            $table->index(['kategori_id', 'is_active']); // Index untuk performa kategori
        });

        // Tryouts table indexes
        Schema::table('tryouts', function (Blueprint $table) {
            $table->index(['is_active', 'created_at']); // Index untuk tryout stats
        });

        // User tryout sessions table indexes
        Schema::table('user_tryout_sessions', function (Blueprint $table) {
            $table->index(['status', 'finished_at']); // Index untuk peserta aktif dan selesai
            $table->index(['user_id', 'status']); // Index untuk user sessions
        });

        // User tryout soal table indexes
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->index(['soal_id', 'skor']); // Index untuk performa kategori
            $table->index(['user_id', 'tryout_id']); // Index untuk user progress
        });

        // Hasil tes table indexes
        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->index(['user_id', 'skor_akhir']); // Index untuk top performers
            $table->index('skor_akhir'); // Index untuk distribusi skor
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email', 'role']);
            $table->dropIndex(['is_active', 'created_at']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'payment_status', 'end_date']);
            $table->dropIndex(['payment_status', 'created_at']);
            $table->dropIndex(['package_type']);
        });

        Schema::table('soals', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'kategori_id']);
            $table->dropIndex(['kategori_id', 'is_active']);
        });

        Schema::table('tryouts', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'created_at']);
        });

        Schema::table('user_tryout_sessions', function (Blueprint $table) {
            $table->dropIndex(['status', 'finished_at']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->dropIndex(['soal_id', 'skor']);
            $table->dropIndex(['user_id', 'tryout_id']);
        });

        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'skor_akhir']);
            $table->dropIndex(['skor_akhir']);
        });
    }
};