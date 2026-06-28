<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->string('tagline', 125)->nullable()->after('name');
            $table->text('features')->nullable()->after('description');
            $table->string('special_offer_text', 255)->nullable()->after('features');
            $table->boolean('special_offer_included')->default(false)->after('special_offer_text');
            $table->string('theme_color', 20)->default('#007BFF')->after('special_offer_included');
            $table->boolean('is_featured')->default(false)->after('theme_color');
        });
    }

    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn([
                'tagline',
                'features',
                'special_offer_text',
                'special_offer_included',
                'theme_color',
                'is_featured',
            ]);
        });
    }
};
