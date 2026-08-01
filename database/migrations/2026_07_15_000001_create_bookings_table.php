<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();

            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->string('pickup_location');
            $table->text('destination_itinerary');

            $table->string('preferred_vehicle');
            $table->unsignedSmallInteger('passengers_count');

            $table->date('return_date');
            $table->time('return_time');

            $table->string('handover_option');

            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
