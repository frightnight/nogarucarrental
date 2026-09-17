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
        Schema::create('driver_evaluations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('driver_license_number');
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluated_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('overall_rating');
            $table->unsignedTinyInteger('punctuality_rating')->nullable();
            $table->unsignedTinyInteger('safety_rating')->nullable();
            $table->unsignedTinyInteger('service_rating')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('driver_license_number')->references('license_number')->on('drivers')->cascadeOnDelete();
            $table->unique(['booking_id', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_evaluations');
    }
};
