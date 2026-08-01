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
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('final_rate');
            $table->decimal('pickup_fee', 10, 2)->default(0)->after('delivery_fee');
            $table->decimal('reservation_fee', 10, 2)->default(0)->after('pickup_fee');
            $table->text('special_request')->nullable()->after('owner_notes');
            $table->string('flight_details_path')->nullable()->after('special_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_fee',
                'pickup_fee',
                'reservation_fee',
                'special_request',
                'flight_details_path',
            ]);
        });
    }
};
