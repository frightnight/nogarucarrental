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
        Schema::table('quotations', function (Blueprint $table) {
            if (! Schema::hasColumn('quotations', 'quotation_footnote_id')) {
                $table->unsignedBigInteger('quotation_footnote_id')->nullable()->after('driver_license_number');
            }

            if (! Schema::hasColumn('quotations', 'footnote_content')) {
                $table->longText('footnote_content')->nullable()->after('itinerary_end_longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('quotation_footnote_id');
            $table->dropColumn('footnote_content');
        });
    }
};
