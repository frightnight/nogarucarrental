<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('sales_agent_status')->nullable()->after('password');
            $table->string('phone')->nullable()->after('email');
        });

        Schema::table('sales_leads', function (Blueprint $table): void {
            $table->date('rental_start_date')->nullable()->after('source');
            $table->date('rental_end_date')->nullable()->after('rental_start_date');
            $table->string('preferred_vehicle')->nullable()->after('rental_end_date');
            $table->decimal('estimated_value', 12, 2)->nullable()->after('preferred_vehicle');
            $table->foreignId('quotation_id')->nullable()->after('converted_booking_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales_leads', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('quotation_id');
            $table->dropColumn(['rental_start_date', 'rental_end_date', 'preferred_vehicle', 'estimated_value']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['sales_agent_status', 'phone']);
        });
    }
};
