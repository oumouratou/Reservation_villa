<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('address');
            $table->string('city', 100);
            $table->string('country', 100);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('weekend_price', 10, 2)->nullable();
            $table->decimal('cleaning_fee', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->unsignedSmallInteger('max_guests');
            $table->unsignedSmallInteger('bedrooms');
            $table->unsignedSmallInteger('bathrooms');
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('rules')->nullable();
            $table->enum('cancellation_policy', ['flexible', 'moderate', 'strict'])->default('moderate');
            $table->unsignedSmallInteger('min_nights')->default(1);
            $table->unsignedSmallInteger('max_nights')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['city', 'country']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villas');
    }
};
