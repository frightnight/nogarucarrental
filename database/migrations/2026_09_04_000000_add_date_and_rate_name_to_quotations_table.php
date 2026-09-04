<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table): void {
            $table->date('quotation_date')->nullable()->after('quotation_number');
            $table->string('vehicle_rate_name')->nullable()->after('total_distance_km');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table): void {
            $table->dropColumn(['quotation_date', 'vehicle_rate_name']);
        });
    }
};
