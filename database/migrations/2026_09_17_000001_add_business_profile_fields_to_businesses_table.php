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
            $table->string('business_type')->nullable()->after('city');
            $table->string('business_category')->nullable()->after('business_type');
            $table->string('business_address')->nullable()->after('business_category');
            $table->string('contact_number')->nullable()->after('business_address');
            $table->string('registration_number')->nullable()->after('contact_number');
            $table->string('tin')->nullable()->after('registration_number');
            $table->string('permit_issuer')->nullable()->after('tin');
            $table->json('permit_images')->nullable()->after('permit_issuer');
            $table->timestamp('profile_completed_at')->nullable()->after('permit_images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table): void {
            $table->dropColumn([
                'business_type',
                'business_category',
                'business_address',
                'contact_number',
                'registration_number',
                'tin',
                'permit_issuer',
                'permit_images',
                'profile_completed_at',
            ]);
        });
    }
};
