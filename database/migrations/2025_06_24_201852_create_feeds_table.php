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
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_kegiatan_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['post', 'event']);
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            
            // Event-specific fields (nullable for posts)
            $table->date('event_date')->nullable();
            $table->enum('event_type', ['online', 'offline'])->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->unsignedInteger('price')->nullable();
            $table->json('payment_methods')->nullable();
            
            $table->timestamps();
            
            $table->index('unit_kegiatan_id');
            $table->index('type');
            $table->index('event_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeds');
    }
};
