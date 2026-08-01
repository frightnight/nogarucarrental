<?php

namespace Database\Seeders;

use App\BusinessPlan;
use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class BusinessPlanDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::findOrCreate('business_owner');

        $accounts = [
            ['plan' => BusinessPlan::Free, 'name' => 'Free Plan Rentals', 'slug' => 'free-plan-rentals', 'email' => 'free.owner@example.com', 'vehicles' => 3],
            ['plan' => BusinessPlan::Basic, 'name' => 'Basic Plan Rentals', 'slug' => 'basic-plan-rentals', 'email' => 'basic.owner@example.com', 'vehicles' => 8],
            ['plan' => BusinessPlan::Pro, 'name' => 'Pro Plan Rentals', 'slug' => 'pro-plan-rentals', 'email' => 'pro.owner@example.com', 'vehicles' => 18],
            ['plan' => BusinessPlan::Business, 'name' => 'Business Plan Rentals', 'slug' => 'business-plan-rentals', 'email' => 'business.owner@example.com', 'vehicles' => 30],
        ];

        foreach ($accounts as $account) {
            $business = Business::updateOrCreate(
                ['slug' => $account['slug']],
                [
                    'name' => $account['name'],
                    'city' => 'Legazpi City',
                    'description' => "Demo {$account['plan']->label()} plan business.",
                    'plan' => $account['plan'],
                ],
            );

            $owner = User::firstOrCreate(
                ['email' => $account['email']],
                ['name' => "{$account['plan']->label()} Plan Owner", 'password' => bcrypt('password')],
            );
            $owner->assignRole('business_owner');

            BusinessUser::firstOrCreate([
                'business_id' => $business->id,
                'user_id' => $owner->id,
                'business_role' => 'owner',
            ]);

            for ($number = 1; $number <= $account['vehicles']; $number++) {
                $car = Car::firstOrCreate(
                    ['business_id' => $business->id, 'car_model' => "{$account['plan']->label()} Demo Vehicle {$number}"],
                    [
                        'owner_id' => $owner->id,
                        'vehicle_type' => $number % 3 === 0 ? 'SUV' : 'Sedan',
                        'variant' => 'Demo',
                        'transmission' => 'Automatic',
                        'seats' => 5,
                        'rental_type' => 'Daily',
                        'status' => 'available',
                        'plate_number' => strtoupper($account['plan']->value).'-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                        'year_model' => 2025,
                        'ownership' => 'owned',
                    ],
                );

                $car->rates()->updateOrCreate(['name' => '12hrs'], ['value' => 1800]);
                $car->rates()->updateOrCreate(['name' => '24hrs'], ['value' => 2800]);
                $car->rates()->updateOrCreate(['name' => 'Extension per hour'], ['value' => 250]);
            }
        }
    }
}
