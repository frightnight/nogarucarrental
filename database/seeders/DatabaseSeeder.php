<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\PlatformLandingPage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        PlatformLandingPage::query()->firstOrCreate([
            'id' => 1,
        ], [
            'content' => [
                'hero_eyebrow' => 'Your trip starts here',
                'hero_title' => 'The road is yours.',
                'hero_highlight' => 'Make it memorable.',
                'hero_subtitle' => 'Find a dependable ride from local rental companies, wherever your next adventure takes you.',
                'company_heading' => 'Great rentals, from people nearby.',
                'destination_heading' => 'Where will the road take you?',
                'vehicle_heading' => 'Popular vehicles this week.',
                'why_heading' => 'Car rental without the runaround.',
                'testimonial' => ['quote' => 'Booking a car for our family trip took minutes. The choices were clear and the rental company was fantastic.', 'name' => 'Maria Santos', 'role' => 'Verified renter · Cebu'],
                'pricing_heading' => 'A better way to grow your fleet.',
                'faq_heading' => 'Frequently asked questions.',
                'footer_text' => 'Making local car rentals easier, one trip at a time.',
            ],
            'featured_business_slugs' => ['metro-rentals', 'sunrise-car-rentals', 'oceanview-rentals', 'mountain-mobility'],
            'featured_car_ids' => [],
        ]);

        $roles = ['administrator', 'moderator', 'business_owner', 'business_moderator', 'booker', 'agent', 'client'];
        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName);
        }

        $defaultPassword = bcrypt('password');

        // ---------
        // Admins
        // ---------
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Administrator', 'password' => $defaultPassword]
        );
        $admin->assignRole('administrator');

        $moderator = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            ['name' => 'Moderator', 'password' => $defaultPassword]
        );
        $moderator->assignRole('moderator');

        // Keep existing single demo accounts (if they already exist)
        $businessOwner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            ['name' => 'Business Owner', 'password' => $defaultPassword]
        );
        $businessOwner->assignRole('business_owner');

        $businessModerator = User::firstOrCreate(
            ['email' => 'business_moderator@example.com'],
            ['name' => 'Business Moderator', 'password' => $defaultPassword]
        );
        $businessModerator->assignRole('business_moderator');

        $booker = User::firstOrCreate(
            ['email' => 'booker@example.com'],
            ['name' => 'Booker', 'password' => $defaultPassword]
        );
        $booker->assignRole('booker');

        $agent = User::firstOrCreate(
            ['email' => 'agent@example.com'],
            ['name' => 'Agent', 'password' => $defaultPassword]
        );
        $agent->assignRole('agent');

        $client = User::firstOrCreate(
            ['email' => 'client@example.com'],
            ['name' => 'Client', 'password' => $defaultPassword]
        );
        $client->assignRole('client');

        // -----------------------------
        // 5 Businesses + their staff
        // -----------------------------
        $businessNames = [
            'Metro Rentals',
            'Sunrise Car Rentals',
            'Oceanview Rentals',
            'Mountain Mobility',
            'Cityline Rides',
        ];

        foreach ($businessNames as $index => $name) {
            $i = $index + 1;
            $slug = strtolower(str_replace(' ', '-', $name));

            $business = Business::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'city' => null,
                    'description' => 'Demo business #'.$i,
                ]
            );

            // Business owner (role: business_owner)
            $ownerEmail = "owner{$i}@example.com";
            $ownerName = "Business Owner {$i}";
            $ownerUser = User::firstOrCreate(
                ['email' => $ownerEmail],
                ['name' => $ownerName, 'password' => $defaultPassword]
            );
            $ownerUser->assignRole('business_owner');

            // Business moderator (role: business_moderator)
            $businessModeratorEmail = "business_moderator{$i}@example.com";
            $businessModeratorName = "Business Moderator {$i}";
            $businessModeratorUser = User::firstOrCreate(
                ['email' => $businessModeratorEmail],
                ['name' => $businessModeratorName, 'password' => $defaultPassword]
            );
            $businessModeratorUser->assignRole('business_moderator');

            // Booker (role: booker)
            $bookerEmail = "booker{$i}@example.com";
            $bookerName = "Booker {$i}";
            $bookerUser = User::firstOrCreate(
                ['email' => $bookerEmail],
                ['name' => $bookerName, 'password' => $defaultPassword]
            );
            $bookerUser->assignRole('booker');

            // Agent (role: agent)
            $agentEmail = "agent{$i}@example.com";
            $agentName = "Agent {$i}";
            $agentUser = User::firstOrCreate(
                ['email' => $agentEmail],
                ['name' => $agentName, 'password' => $defaultPassword]
            );
            $agentUser->assignRole('agent');

            // Pivot rows for each business
            BusinessUser::firstOrCreate([
                'business_id' => $business->id,
                'user_id' => $ownerUser->id,
                'business_role' => 'owner',
            ]);

            BusinessUser::firstOrCreate([
                'business_id' => $business->id,
                'user_id' => $bookerUser->id,
                'business_role' => 'booker',
            ]);

            BusinessUser::firstOrCreate([
                'business_id' => $business->id,
                'user_id' => $agentUser->id,
                'business_role' => 'agent',
            ]);
        }

        // -----------------
        // 5 Client accounts
        // -----------------
        for ($i = 1; $i <= 5; $i++) {
            $clientEmail = "client{$i}@example.com";
            $clientName = "Client {$i}";

            $clientUser = User::firstOrCreate(
                ['email' => $clientEmail],
                ['name' => $clientName, 'password' => $defaultPassword]
            );
            $clientUser->assignRole('client');
        }

        $this->call([
            BusinessPlanDemoSeeder::class,
            CarSeeder::class,
            CarImageSeeder::class,
            RateSeeder::class,
        ]);
    }
}
