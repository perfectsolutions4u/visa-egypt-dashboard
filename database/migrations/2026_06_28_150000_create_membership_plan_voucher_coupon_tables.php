<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('membership_plan_voucher')) {
            Schema::create('membership_plan_voucher', function (Blueprint $table) {
                $table->id();
                $table->foreignId('membership_plan_id')->constrained('membership_plans')->cascadeOnDelete();
                $table->foreignId('voucher_id')->constrained('visa_vouchers')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['membership_plan_id', 'voucher_id']);
            });
        }

        if (! Schema::hasTable('membership_plan_coupon')) {
            Schema::create('membership_plan_coupon', function (Blueprint $table) {
                $table->id();
                $table->foreignId('membership_plan_id')->constrained('membership_plans')->cascadeOnDelete();
                $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['membership_plan_id', 'coupon_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_plan_coupon');
        Schema::dropIfExists('membership_plan_voucher');
    }
};
