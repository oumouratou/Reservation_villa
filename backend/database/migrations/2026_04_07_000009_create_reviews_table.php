<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('rating', 3, 1);
            $table->decimal('cleanliness', 3, 1);
            $table->decimal('accuracy', 3, 1);
            $table->decimal('communication', 3, 1);
            $table->decimal('location', 3, 1);
            $table->decimal('value', 3, 1);
            $table->text('comment');
            $table->text('owner_response')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->unique('reservation_id');
            $table->index(['villa_id', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
