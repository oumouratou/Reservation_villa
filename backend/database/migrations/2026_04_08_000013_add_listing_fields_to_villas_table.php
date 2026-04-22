<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('villas', function (Blueprint $table) {
            $table->enum('listing_type', ['vente', 'location'])->default('location')->after('status');
            $table->decimal('price', 12, 2)->nullable()->after('price_per_night');
            $table->decimal('surface', 8, 2)->nullable()->after('bathrooms');
            $table->unsignedSmallInteger('rooms_count')->nullable()->after('surface');

            $table->boolean('has_pool')->default(false)->after('rooms_count');
            $table->boolean('has_garden')->default(false)->after('has_pool');
            $table->boolean('has_garage')->default(false)->after('has_garden');
            $table->boolean('has_air_conditioning')->default(false)->after('has_garage');
            $table->boolean('has_wifi')->default(false)->after('has_air_conditioning');
        });
    }

    public function down(): void
    {
        Schema::table('villas', function (Blueprint $table) {
            $table->dropColumn([
                'listing_type',
                'price',
                'surface',
                'rooms_count',
                'has_pool',
                'has_garden',
                'has_garage',
                'has_air_conditioning',
                'has_wifi',
            ]);
        });
    }
};
