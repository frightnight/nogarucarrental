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
        Schema::table('cars', function (Blueprint $table): void {
            $table->string('fuel_type')->nullable()->after('transmission');
            $table->unsignedSmallInteger('fuel_tank_capacity_liters')->nullable()->after('fuel_type');
            $table->unsignedTinyInteger('fuel_display_bar')->nullable()->after('fuel_tank_capacity_liters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table): void {
            $table->dropColumn(['fuel_type', 'fuel_tank_capacity_liters', 'fuel_display_bar']);
        });
    }
};
