<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('hero_title')->nullable()->after('description');
            $table->text('hero_subtitle')->nullable()->after('hero_title');
            $table->string('about_title')->nullable()->after('hero_subtitle');
            $table->text('about_content')->nullable()->after('about_title');
            $table->json('about_features')->nullable()->after('about_content');
            $table->string('contact_email')->nullable()->after('about_features');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->text('contact_address')->nullable()->after('contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title',
                'hero_subtitle',
                'about_title',
                'about_content',
                'about_features',
                'contact_email',
                'contact_phone',
                'contact_address',
            ]);
        });
    }
};
