<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('icon', 50)->nullable();
            $table->string('category', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('villa_amenity', function (Blueprint $table) {
            $table->foreignId('villa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->primary(['villa_id', 'amenity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villa_amenity');
        Schema::dropIfExists('amenities');
    }
};
