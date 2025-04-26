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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(UnitKegiatan::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text("description");
            $table->date("event_date");
            $table->enum("event_type", ["online", "offline"]);
            $table->string("poster")->nullable();
            $table->string("location")->nullable();
            $table->boolean("is_paid")->default(false);
            $table->unsignedInteger('price')->nullable();
            $table->string('payment_methods')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
