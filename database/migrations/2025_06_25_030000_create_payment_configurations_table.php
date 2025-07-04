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
        Schema::create('payment_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_kegiatan_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Event Registration", "Membership Fee", "Workshop Payment"
            // $table->text('description')->nullable();
            $table->decimal('amount', 10, 2); // Amount in IDR
            $table->enum('currency', ['IDR'])->default('IDR');
            $table->boolean('is_active')->default(true);
            $table->json('payment_methods')->nullable(); // Flexible payment methods
            $table->json('custom_fields')->nullable(); // Dynamic fields like "Student ID", "Phone", etc.
            $table->timestamps();

            // Indexes for performance
            $table->index(['unit_kegiatan_id', 'is_active']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_configurations');
    }
};
