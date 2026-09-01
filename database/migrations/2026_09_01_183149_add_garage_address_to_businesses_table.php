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
            $table->string('garage_address')->nullable()->after('contact_address');
            $table->decimal('garage_latitude', 10, 7)->nullable()->after('garage_address');
            $table->decimal('garage_longitude', 10, 7)->nullable()->after('garage_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['garage_address', 'garage_latitude', 'garage_longitude']);
        });
    }
};
