<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarImageSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * Seeds 5 sample images per car using picsum.photos placeholders.
     */
    public function run(): void
    {
        $cars = Car::all();

        foreach ($cars as $car) {
            // Create a unique seed key per car for consistent placeholder images
            $seed = 'car-'.$car->id.'-'.md5($car->car_model ?? 'car'.$car->id);

            for ($i = 1; $i <= 5; $i++) {
                CarImage::firstOrCreate(
                    [
                        'car_id' => $car->id,
                        'image_path' => "https://picsum.photos/seed/{$seed}-{$i}/640/480",
                    ]
                );
            }
        }
    }
}
