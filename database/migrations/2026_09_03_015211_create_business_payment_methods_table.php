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
        Schema::create('business_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method');
            $table->string('account_type');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('swift_code')->nullable();
            $table->string('account_category');
            $table->string('currency', 12);
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_payment_methods');
    }
};
