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
        Schema::table('drivers', function (Blueprint $table): void {
            $table->string('driver_code')->nullable()->unique()->after('license_number');
            $table->foreignId('user_id')->nullable()->unique()->after('driver_code')->constrained()->nullOnDelete();
            $table->string('middle_name')->nullable()->after('full_name');
            $table->string('suffix')->nullable()->after('middle_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('license_type')->nullable();
            $table->date('license_issued_at')->nullable();
            $table->date('license_expires_at')->nullable();
            $table->string('license_restrictions')->nullable();
            $table->string('license_front_path')->nullable();
            $table->string('license_back_path')->nullable();
            $table->string('employment_type')->nullable();
            $table->date('date_hired')->nullable();
            $table->string('employment_status')->default('applicant');
            $table->unsignedTinyInteger('years_driving_experience')->nullable();
            $table->unsignedTinyInteger('professional_driving_experience')->nullable();
            $table->json('vehicle_experience')->nullable();
            $table->json('service_experience')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('emergency_contact_address')->nullable();
            $table->string('approval_status')->default('approved');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropUnique(['driver_code']);
            $table->dropColumn([
                'driver_code', 'user_id', 'middle_name', 'suffix', 'date_of_birth', 'gender', 'address', 'city',
                'profile_photo_path', 'license_type', 'license_issued_at', 'license_expires_at', 'license_restrictions',
                'license_front_path', 'license_back_path', 'employment_type', 'date_hired', 'employment_status',
                'years_driving_experience', 'professional_driving_experience', 'vehicle_experience', 'service_experience',
                'emergency_contact_name', 'emergency_contact_relationship', 'emergency_contact_phone', 'emergency_contact_address',
                'approval_status', 'reviewed_at', 'reviewed_by', 'rejection_reason',
            ]);
        });
    }
};
