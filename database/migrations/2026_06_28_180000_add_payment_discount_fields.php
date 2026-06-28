<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('visa_payments')) {
            Schema::table('visa_payments', function (Blueprint $table) {
                if (! Schema::hasColumn('visa_payments', 'discount_type')) {
                    $table->string('discount_type', 20)->nullable()->after('subtotal');
                }
                if (! Schema::hasColumn('visa_payments', 'coupon_id')) {
                    $table->foreignId('coupon_id')->nullable()->after('discount_type')->constrained('coupons')->nullOnDelete();
                }
                if (! Schema::hasColumn('visa_payments', 'voucher_id')) {
                    $table->foreignId('voucher_id')->nullable()->after('coupon_id')->constrained('visa_vouchers')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('client_vouchers') && ! Schema::hasColumn('client_vouchers', 'used_at')) {
            Schema::table('client_vouchers', function (Blueprint $table) {
                $table->timestamp('used_at')->nullable()->after('redeemed_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('client_vouchers') && Schema::hasColumn('client_vouchers', 'used_at')) {
            Schema::table('client_vouchers', function (Blueprint $table) {
                $table->dropColumn('used_at');
            });
        }

        if (Schema::hasTable('visa_payments')) {
            Schema::table('visa_payments', function (Blueprint $table) {
                if (Schema::hasColumn('visa_payments', 'voucher_id')) {
                    $table->dropConstrainedForeignId('voucher_id');
                }
                if (Schema::hasColumn('visa_payments', 'coupon_id')) {
                    $table->dropConstrainedForeignId('coupon_id');
                }
                if (Schema::hasColumn('visa_payments', 'discount_type')) {
                    $table->dropColumn('discount_type');
                }
            });
        }
    }
};
