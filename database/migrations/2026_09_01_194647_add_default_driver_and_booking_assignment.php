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
        Schema::table('business_driver', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('is_available');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('driver_license_number')->nullable()->after('car_id');
            $table->foreign('driver_license_number')->references('license_number')->on('drivers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['driver_license_number']);
            $table->dropColumn('driver_license_number');
        });

        Schema::table('business_driver', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
