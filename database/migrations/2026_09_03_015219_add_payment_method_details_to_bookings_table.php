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
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('business_payment_method_id')->nullable()->after('payment_method')->constrained()->nullOnDelete();
            $table->string('payment_reference_number')->nullable()->after('business_payment_method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_payment_method_id');
            $table->dropColumn('payment_reference_number');
        });
    }
};
