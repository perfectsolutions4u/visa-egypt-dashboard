<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(DestinationSeeder::class);
        $this->call(CurrenciesSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(PagesSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(BlogCategoriesSeeder::class);
        $this->call(AmenitySeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(VisaEgyptSeeder::class);
        $this->call([
            CitySeeder::class,
        ]);
    }
}
