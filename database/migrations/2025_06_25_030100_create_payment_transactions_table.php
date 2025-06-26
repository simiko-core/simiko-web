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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_kegiatan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_configuration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feed_id')->nullable()->constrained()->nullOnDelete(); // Optional link to event
            $table->string('transaction_id')->unique(); // Unique transaction identifier
            $table->decimal('amount', 10, 2);
            $table->enum('currency', ['IDR', 'USD'])->default('IDR');
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled', 'expired'])->default('pending');
            $table->string('payment_method')->nullable(); // Which method was used
            $table->json('payment_details')->nullable(); // Payment method details
            $table->json('custom_data')->nullable(); // Dynamic field data
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['unit_kegiatan_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['payment_configuration_id', 'status']);
            $table->index('transaction_id');
            $table->index('created_at');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
}; 