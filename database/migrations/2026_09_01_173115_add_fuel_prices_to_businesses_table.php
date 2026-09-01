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
        Schema::table('businesses', function (Blueprint $table): void {
            $table->decimal('diesel_premium_price_per_liter', 10, 2)->default(90)->after('contact_address');
            $table->decimal('diesel_regular_price_per_liter', 10, 2)->default(80)->after('diesel_premium_price_per_liter');
            $table->decimal('gasoline_premium_price_per_liter', 10, 2)->default(95)->after('diesel_regular_price_per_liter');
            $table->decimal('gasoline_regular_price_per_liter', 10, 2)->default(85)->after('gasoline_premium_price_per_liter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table): void {
            $table->dropColumn(['diesel_premium_price_per_liter', 'diesel_regular_price_per_liter', 'gasoline_premium_price_per_liter', 'gasoline_regular_price_per_liter']);
        });
    }
};
