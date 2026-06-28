<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Cairo', 'Alexandria', 'Giza', 'Port Said', 'Suez', 'Luxor', 'Aswan', 'Asyut', 'Ismailia', 'Faiyum',
            'Zagazig', 'Damietta', 'Damanhur', 'Banha', 'Kafr El Sheikh', 'Mallawi', 'Hurghada', 'Qena', 'Sohag', 'Minya',
            'Beni Suef', 'Mansoura', 'Tanta', 'Mahalla El Kubra', 'El Arish', 'Sharm El Sheikh', 'Dahab', 'Marsa Alam', 'El Gouna', 'Siwa',
            'New Cairo', '6th of October', 'Obour', 'Shebin El Kom', 'Mit Ghamr', 'Kafr El Dawwar', 'El Mahalla', 'Qalyub', 'Shibin al Kawm', 'Belbeis',
            'Abu Kabir', 'Kafr Saqr', 'Tanta', 'Basyoun', 'Matareya', 'Rosetta', 'Idfu', 'Kom Ombo', 'Manfalut', 'Quesna',
        ];

        foreach ($cities as $city) {
            City::updateOrCreate([
                'slug' => Str::slug($city)
            ], [
                'name' => $city,
            ]);
        }
    }
} 