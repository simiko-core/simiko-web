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
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Make user_id nullable to allow anonymous registrations
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Add reference to anonymous event registrations
            $table->unsignedBigInteger('anonymous_registration_id')->nullable()->after('user_id');
            $table->foreign('anonymous_registration_id')->references('id')->on('anonymous_event_registrations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['anonymous_registration_id']);
            $table->dropColumn('anonymous_registration_id');

            // Make user_id required again
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
