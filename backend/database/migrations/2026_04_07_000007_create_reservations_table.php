<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('traveler_id')->constrained('users')->cascadeOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedSmallInteger('guests');
            $table->unsignedSmallInteger('nights');
            $table->decimal('base_price', 10, 2);
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'completed'])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('special_requests')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->timestamps();

            $table->index(['villa_id', 'status']);
            $table->index(['traveler_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
