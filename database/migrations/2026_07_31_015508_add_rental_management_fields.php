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
        Schema::table('cars', function (Blueprint $table) {
            $table->date('registration_expires_at')->nullable()->after('year_model');
            $table->date('insurance_expires_at')->nullable()->after('registration_expires_at');
        });
        Schema::create('vehicle_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inspected_by')->constrained('users')->cascadeOnDelete();
            $table->string('stage');
            $table->json('checklist')->nullable();
            $table->text('damage_notes')->nullable();
            $table->json('photo_paths')->nullable();
            $table->timestamps();
            $table->unique(['booking_id', 'stage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_inspections');
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['registration_expires_at', 'insurance_expires_at']);
        });
    }
};
