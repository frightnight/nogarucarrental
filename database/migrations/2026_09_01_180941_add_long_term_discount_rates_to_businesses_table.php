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
        Schema::table('businesses', function (Blueprint $table) {
            $table->decimal('discount_7_to_14_days_percent', 5, 2)->default(10)->after('gasoline_regular_price_per_liter');
            $table->decimal('discount_15_to_24_days_percent', 5, 2)->default(20)->after('discount_7_to_14_days_percent');
            $table->decimal('discount_25_to_31_days_percent', 5, 2)->default(30)->after('discount_15_to_24_days_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'discount_7_to_14_days_percent',
                'discount_15_to_24_days_percent',
                'discount_25_to_31_days_percent',
            ]);
        });
    }
};
