<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if user_id column exists
        if (Schema::hasColumn('payment_transactions', 'user_id')) {
            // Drop foreign key if it exists
            Schema::table('payment_transactions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        // Make anonymous_registration_id required (not nullable)
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('anonymous_registration_id')->nullable(false)->change();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Add user_id back
            $table->unsignedBigInteger('user_id')->nullable()->after('unit_kegiatan_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Make anonymous_registration_id nullable again
            $table->unsignedBigInteger('anonymous_registration_id')->nullable()->change();
        });
    }
};
