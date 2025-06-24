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
        Schema::table('feeds', function (Blueprint $table) {
            $table->index(['unit_kegiatan_id', 'created_at'], 'feeds_ukm_created_idx');
            $table->index(['type', 'created_at'], 'feeds_type_created_idx');
            $table->index(['event_date'], 'feeds_event_date_idx');
        });

        Schema::table('pendaftaran_anggotas', function (Blueprint $table) {
            $table->index(['status'], 'pendaftaran_status_idx');
            $table->index(['unit_kegiatan_id', 'status'], 'pendaftaran_ukm_status_idx');
        });

        Schema::table('achievements', function (Blueprint $table) {
            $table->index(['unit_kegiatan_id', 'created_at'], 'achievements_ukm_created_idx');
        });

        Schema::table('activity_galleries', function (Blueprint $table) {
            $table->index(['unit_kegiatan_id', 'created_at'], 'gallery_ukm_created_idx');
        });

        Schema::table('unit_kegiatan_profiles', function (Blueprint $table) {
            $table->index(['unit_kegiatan_id', 'period'], 'profiles_ukm_period_idx');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->index(['active'], 'banners_active_idx');
            $table->index(['feed_id', 'active'], 'banners_feed_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropIndex('feeds_ukm_created_idx');
            $table->dropIndex('feeds_type_created_idx');
            $table->dropIndex('feeds_event_date_idx');
        });

        Schema::table('pendaftaran_anggotas', function (Blueprint $table) {
            $table->dropIndex('pendaftaran_status_idx');
            $table->dropIndex('pendaftaran_ukm_status_idx');
        });

        Schema::table('achievements', function (Blueprint $table) {
            $table->dropIndex('achievements_ukm_created_idx');
        });

        Schema::table('activity_galleries', function (Blueprint $table) {
            $table->dropIndex('gallery_ukm_created_idx');
        });

        Schema::table('unit_kegiatan_profiles', function (Blueprint $table) {
            $table->dropIndex('profiles_ukm_period_idx');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropIndex('banners_active_idx');
            $table->dropIndex('banners_feed_active_idx');
        });
    }
};
