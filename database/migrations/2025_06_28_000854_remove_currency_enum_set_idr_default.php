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
        // Remove currency enum from payment_configurations and set default to IDR
        Schema::table('payment_configurations', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('payment_configurations', function (Blueprint $table) {
            $table->string('currency')->default('IDR')->after('amount');
        });

        // Remove currency enum from payment_transactions and set default to IDR  
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('currency')->default('IDR')->after('amount');
        });

        // Update all existing records to use IDR
        DB::table('payment_configurations')->update(['currency' => 'IDR']);
        DB::table('payment_transactions')->update(['currency' => 'IDR']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert payment_configurations back to enum
        Schema::table('payment_configurations', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('payment_configurations', function (Blueprint $table) {
            $table->enum('currency', ['IDR', 'USD'])->default('IDR')->after('amount');
        });

        // Revert payment_transactions back to enum
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->enum('currency', ['IDR', 'USD'])->default('IDR')->after('amount');
        });
    }
};
