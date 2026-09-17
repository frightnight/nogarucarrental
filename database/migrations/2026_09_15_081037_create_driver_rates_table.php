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
        Schema::create('driver_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('driver_license_number');
            $table->string('trip_type');
            $table->string('rate_type')->default('per_trip');
            $table->decimal('amount', 10, 2);
            $table->decimal('overtime_rate', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('driver_license_number')->references('license_number')->on('drivers')->cascadeOnDelete();
            $table->unique(['driver_license_number', 'trip_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_rates');
    }
};
