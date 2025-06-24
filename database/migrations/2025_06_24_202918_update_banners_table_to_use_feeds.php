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
        Schema::table('banners', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['post_id']);
            
            // Rename the column
            $table->renameColumn('post_id', 'feed_id');
            
            // Add new foreign key constraint
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['feed_id']);
            
            // Rename the column back
            $table->renameColumn('feed_id', 'post_id');
            
            // Add back the old foreign key constraint
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }
};
