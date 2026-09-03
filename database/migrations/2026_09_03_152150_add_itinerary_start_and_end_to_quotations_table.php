<?php

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
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('itinerary_start_address')->nullable()->after('itinerary');
            $table->decimal('itinerary_start_latitude', 10, 7)->nullable()->after('itinerary_start_address');
            $table->decimal('itinerary_start_longitude', 10, 7)->nullable()->after('itinerary_start_latitude');
            $table->string('itinerary_end_address')->nullable()->after('itinerary_start_longitude');
            $table->decimal('itinerary_end_latitude', 10, 7)->nullable()->after('itinerary_end_address');
            $table->decimal('itinerary_end_longitude', 10, 7)->nullable()->after('itinerary_end_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'itinerary_start_address',
                'itinerary_start_latitude',
                'itinerary_start_longitude',
                'itinerary_end_address',
                'itinerary_end_latitude',
                'itinerary_end_longitude',
            ]);
        });
    }
};
