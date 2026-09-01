<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $timestamp = now();
        DB::table('business_plans')->upsert([
            ['name' => 'Free', 'slug' => 'free', 'price' => 0, 'vehicle_limit' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Basic', 'slug' => 'basic', 'price' => 499, 'vehicle_limit' => 10, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Pro', 'slug' => 'pro', 'price' => 999, 'vehicle_limit' => 25, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'Business', 'slug' => 'business', 'price' => 1999, 'vehicle_limit' => null, 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ], ['slug'], ['name', 'price', 'vehicle_limit', 'updated_at']);

        Schema::table('businesses', function (Blueprint $table) {
            $table->foreignId('business_plan_id')->nullable()->after('plan')->constrained()->nullOnDelete();
        });

        $planIds = DB::table('business_plans')->pluck('id', 'slug');
        foreach ($planIds as $slug => $id) {
            DB::table('businesses')->where('plan', $slug)->update(['business_plan_id' => $id]);
        }

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('plan')->default('free')->after('description');
        });

        $planIds = DB::table('business_plans')->pluck('id', 'slug');
        foreach ($planIds as $slug => $id) {
            DB::table('businesses')->where('business_plan_id', $id)->update(['plan' => $slug]);
        }

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_plan_id');
        });
    }
};
