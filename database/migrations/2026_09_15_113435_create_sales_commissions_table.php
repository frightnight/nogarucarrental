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
        Schema::create('sales_commissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('rate_percent', 5, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['sales_agent_id', 'booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_commissions');
    }
};
