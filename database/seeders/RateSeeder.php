<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Car;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    /**
     * @var array<int, array{vehicle_type: string, car_model: string, transmission: string, rates: array<string, float>}>
     */
    private array $vehicles = [
        ['vehicle_type' => 'SUV', 'car_model' => 'Ford Everest', 'transmission' => 'Automatic', 'rates' => ['24hrs' => 4500.00, '12hrs' => 4000.00, 'Sorsogon/CamSur surcharge' => 1000.00]],
        ['vehicle_type' => 'SUV', 'car_model' => 'Mitsubishi Montero Sport', 'transmission' => 'Automatic', 'rates' => ['24hrs' => 4000.00, '12hrs' => 3500.00, 'Sorsogon/CamSur surcharge' => 800.00]],
        ['vehicle_type' => 'MPV', 'car_model' => 'Hyundai Stargazer', 'transmission' => 'Automatic', 'rates' => ['24hrs' => 3500.00, '12hrs' => 3000.00, 'Sorsogon/CamSur surcharge' => 1000.00]],
        ['vehicle_type' => 'MPV', 'car_model' => 'Mitsubishi Xpander', 'transmission' => 'Automatic', 'rates' => ['24hrs' => 3500.00, '12hrs' => 3000.00, 'Sorsogon/CamSur surcharge' => 1000.00]],
        ['vehicle_type' => 'MPV', 'car_model' => 'Toyota Innova M/T', 'transmission' => 'Manual', 'rates' => ['24hrs' => 4000.00, '12hrs' => 3500.00, 'Sorsogon/CamSur surcharge' => 800.00]],
        ['vehicle_type' => 'Van', 'car_model' => 'Hiace Commuter Decontent', 'transmission' => 'Manual', 'rates' => ['24hrs' => 5000.00, '12hrs' => 4500.00, 'Sorsogon/CamSur surcharge' => 1000.00]],
        ['vehicle_type' => 'Sedan', 'car_model' => 'Toyota Vios AT', 'transmission' => 'Automatic', 'rates' => ['24hrs' => 2500.00, '12hrs' => 2000.00, 'Sorsogon/CamSur surcharge' => 500.00]],
        ['vehicle_type' => 'Budget Car', 'car_model' => 'Toyota Wigo MT', 'transmission' => 'Manual', 'rates' => ['24hrs' => 2000.00, '12hrs' => 1500.00, 'Sorsogon/CamSur surcharge' => 500.00]],
        ['vehicle_type' => 'Budget Car', 'car_model' => 'Mitsubishi Mirage AT', 'transmission' => 'Automatic', 'rates' => ['24hrs' => 2000.00, '12hrs' => 1500.00, 'Sorsogon/CamSur surcharge' => 500.00]],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $business = Business::where('slug', 'metro-rentals')->first();

        if (! $business) {
            return;
        }

        foreach ($this->vehicles as $vehicleData) {
            $car = Car::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'car_model' => $vehicleData['car_model'],
                ],
                [
                    'vehicle_type' => $vehicleData['vehicle_type'],
                    'transmission' => $vehicleData['transmission'],
                    'rental_type' => 'Daily',
                    'ownership' => 'owned',
                ],
            );

            foreach ($vehicleData['rates'] as $name => $value) {
                $car->rates()->updateOrCreate(['name' => $name], ['value' => $value]);
            }
        }
    }
}
