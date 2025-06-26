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
            $table->foreignId('payment_configuration_id')->nullable()->after('unit_kegiatan_id')->constrained('payment_configurations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropForeign(['payment_configuration_id']);
            $table->dropColumn('payment_configuration_id');
        });
    }
};
