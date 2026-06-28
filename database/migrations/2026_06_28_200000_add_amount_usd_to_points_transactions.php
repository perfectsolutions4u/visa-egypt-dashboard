<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('points_transactions')) {
            return;
        }

        Schema::table('points_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('points_transactions', 'amount_usd')) {
                $table->decimal('amount_usd', 12, 2)->nullable()->after('points');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('points_transactions')) {
            return;
        }

        Schema::table('points_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('points_transactions', 'amount_usd')) {
                $table->dropColumn('amount_usd');
            }
        });
    }
};
