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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->string('driver_license_number')->nullable();
            $table->string('quotation_number')->unique();
            $table->string('package_type');
            $table->json('itinerary');
            $table->decimal('total_distance_km', 10, 2)->default(0);
            $table->decimal('vehicle_rate', 12, 2)->default(0);
            $table->decimal('driver_rate', 12, 2)->default(0);
            $table->decimal('distance_rate', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
