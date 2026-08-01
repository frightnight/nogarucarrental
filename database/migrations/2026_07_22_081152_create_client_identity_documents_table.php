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
        Schema::create('client_identity_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_profile_id')->constrained()->onDelete('cascade');
            $table->string('valid_id_1_path')->nullable();
            $table->string('valid_id_2_path')->nullable();
            $table->string('selfie_with_ids_path')->nullable();
            $table->string('ltms_welcome_path')->nullable();
            $table->string('ltms_client_id_path')->nullable();
            $table->string('ltms_license_front_path')->nullable();
            $table->string('ltms_license_back_path')->nullable();
            $table->string('proof_of_billing_path')->nullable();
            $table->boolean('is_complete')->default(false)->comment('Whether all required docs have been submitted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_identity_documents');
    }
};
