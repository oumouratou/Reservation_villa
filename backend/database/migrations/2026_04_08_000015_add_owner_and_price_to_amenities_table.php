<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('amenities', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->decimal('price', 10, 2)->default(0)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('amenities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
            $table->dropColumn('price');
        });
    }
};
