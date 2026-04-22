<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_available')->default(true);
            $table->decimal('custom_price', 10, 2)->nullable();
            $table->string('note', 100)->nullable();
            $table->timestamps();

            $table->unique(['villa_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
