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
        Schema::create('sales_leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('converted_booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('new');
            $table->text('notes')->nullable();
            $table->date('next_follow_up_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_leads');
    }
};
