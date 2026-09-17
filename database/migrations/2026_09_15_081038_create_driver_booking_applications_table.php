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
        Schema::create('driver_booking_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('driver_license_number');
            $table->foreignId('driver_rate_id')->nullable()->constrained('driver_rates')->nullOnDelete();
            $table->decimal('quoted_rate', 10, 2);
            $table->decimal('quoted_overtime_rate', 10, 2)->default(0);
            $table->text('message')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('driver_license_number')->references('license_number')->on('drivers')->cascadeOnDelete();
            $table->unique(['booking_id', 'driver_license_number'], 'driver_booking_application_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_booking_applications');
    }
};
