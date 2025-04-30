<?php

use App\Models\UnitKegiatan;
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
        Schema::create('unit_kegiatan_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(UnitKegiatan::class)->constrained()->cascadeOnDelete();
            $table->string("vision");
            $table->string("mission");
            $table->text("description");
            $table->integer("period");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_kegiatan_profiles');
    }
};
