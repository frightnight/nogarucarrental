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
            $table->string('rental_type')->default('self_drive')->after('car_id');
            $table->string('return_location')->nullable()->after('return_time');
            $table->string('handover_other')->nullable()->after('handover_option');
            $table->decimal('initial_rate', 10, 2)->nullable()->after('total_distance_km');
            $table->decimal('final_rate', 10, 2)->nullable()->after('initial_rate');
            $table->text('owner_notes')->nullable()->after('final_rate');
            $table->timestamp('finalized_at')->nullable()->after('owner_notes');
            $table->string('payment_method')->nullable()->after('finalized_at');
            $table->string('payment_proof_path')->nullable()->after('payment_method');
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_proof_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['rental_type', 'return_location', 'handover_other', 'initial_rate', 'final_rate', 'owner_notes', 'finalized_at', 'payment_method', 'payment_proof_path', 'payment_submitted_at']);
        });
    }
};
