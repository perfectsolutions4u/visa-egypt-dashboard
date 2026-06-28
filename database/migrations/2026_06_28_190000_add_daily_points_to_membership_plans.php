<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('membership_plans') && ! Schema::hasColumn('membership_plans', 'daily_points')) {
            Schema::table('membership_plans', function (Blueprint $table) {
                $table->unsignedInteger('daily_points')->default(0)->after('price_usd');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('membership_plans') && Schema::hasColumn('membership_plans', 'daily_points')) {
            Schema::table('membership_plans', function (Blueprint $table) {
                $table->dropColumn('daily_points');
            });
        }
    }
};
