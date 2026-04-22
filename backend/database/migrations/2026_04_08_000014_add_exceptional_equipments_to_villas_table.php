<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('villas', function (Blueprint $table) {
            $table->json('exceptional_equipments')->nullable()->after('has_wifi');
            $table->decimal('exceptional_equipments_total', 12, 2)->default(0)->after('exceptional_equipments');
        });
    }

    public function down(): void
    {
        Schema::table('villas', function (Blueprint $table) {
            $table->dropColumn(['exceptional_equipments', 'exceptional_equipments_total']);
        });
    }
};
