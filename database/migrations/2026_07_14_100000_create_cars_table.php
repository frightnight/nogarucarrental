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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->string('vehicle_type')->nullable();
            $table->string('car_model')->nullable();
            $table->string('variant')->nullable();

            $table->string('transmission')->nullable();
            $table->string('rental_type')->nullable();

            $table->string('plate_number')->nullable();
            $table->unsignedSmallInteger('year_model')->nullable();

            $table->string('ownership')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
