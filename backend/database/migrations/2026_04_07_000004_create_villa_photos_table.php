<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('villa_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->string('cloudinary_id')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('is_cover')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villa_photos');
    }
};
