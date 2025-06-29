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
        Schema::create('anonymous_event_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feed_id');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->json('custom_data')->nullable(); // Store custom field data
            $table->json('custom_files')->nullable(); // Store custom file paths
            $table->timestamps();

            // Foreign keys
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade');

            // Indexes
            $table->index(['feed_id', 'email']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anonymous_event_registrations');
    }
};
