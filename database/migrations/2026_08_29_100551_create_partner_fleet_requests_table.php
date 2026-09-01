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
        Schema::create('partner_fleet_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requesting_business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('partner_business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['requesting_business_id', 'partner_business_id', 'car_id'], 'partner_fleet_request_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_fleet_requests');
    }
};
