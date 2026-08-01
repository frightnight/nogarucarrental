<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->json('itinerary_stops')->nullable()->after('destination_itinerary');
            $table->decimal('total_distance_km', 10, 2)->nullable()->after('itinerary_stops');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['itinerary_stops', 'total_distance_km']);
        });
    }
};
