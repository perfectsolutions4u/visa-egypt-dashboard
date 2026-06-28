<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\City;

return new class extends Migration
{
    public function up(): void
    {
        // Add foreign keys
        Schema::table('trips', function (Blueprint $table) {
            $table->foreignId('departure_city_id')->nullable()->constrained('cities')->onDelete('cascade');
            $table->foreignId('arrival_city_id')->nullable()->constrained('cities')->onDelete('cascade');
        });

        // Drop the old columns
        Schema::table('trips', function (Blueprint $table) {
            if (Schema::hasColumn('trips', 'departure_city')) {
                $table->dropColumn('departure_city');
            }

            if (Schema::hasColumn('trips', 'arrival_city')) {
                $table->dropColumn('arrival_city');
            }
        });
    }

    public function down(): void
    {
    }
};
