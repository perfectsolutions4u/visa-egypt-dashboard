<?php

namespace Database\Seeders;

use Database\Seeders\Permissions\VisaPermissionSeeder as VisaPermissions;
use Illuminate\Database\Seeder;

class VisaPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(VisaPermissions::class);
    }
}
