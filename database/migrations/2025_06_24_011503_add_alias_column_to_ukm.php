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
        Schema::table('unit_kegiatans', function (Blueprint $table) {
            $table->string('alias')->nullable()->after('name');
            $table->index('alias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_kegiatans', function (Blueprint $table) {
            $table->dropIndex(['alias']);
            $table->dropColumn('alias');
        });
    }
};
