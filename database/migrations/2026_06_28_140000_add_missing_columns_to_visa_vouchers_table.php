<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visa_vouchers', function (Blueprint $table) {
            if (! Schema::hasColumn('visa_vouchers', 'service_target')) {
                $table->string('service_target', 50)->nullable()->after('min_amount');
            }

            if (! Schema::hasColumn('visa_vouchers', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('service_target')->constrained('clients')->nullOnDelete();
            }

            if (! Schema::hasColumn('visa_vouchers', 'valid_from')) {
                $table->dateTime('valid_from')->nullable()->after('used_count');
            }

            if (! Schema::hasColumn('visa_vouchers', 'valid_to')) {
                $table->dateTime('valid_to')->nullable()->after('valid_from');
            }
        });

        if (Schema::hasColumn('visa_vouchers', 'expires_at')) {
            DB::table('visa_vouchers')
                ->whereNull('valid_to')
                ->whereNotNull('expires_at')
                ->update(['valid_to' => DB::raw('expires_at')]);

            Schema::table('visa_vouchers', function (Blueprint $table) {
                $table->dropColumn('expires_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('visa_vouchers', function (Blueprint $table) {
            if (! Schema::hasColumn('visa_vouchers', 'expires_at')) {
                $table->dateTime('expires_at')->nullable()->after('used_count');
            }
        });

        if (Schema::hasColumn('visa_vouchers', 'valid_to')) {
            DB::table('visa_vouchers')
                ->whereNotNull('valid_to')
                ->update(['expires_at' => DB::raw('valid_to')]);
        }

        Schema::table('visa_vouchers', function (Blueprint $table) {
            if (Schema::hasColumn('visa_vouchers', 'client_id')) {
                $table->dropConstrainedForeignId('client_id');
            }

            foreach (['valid_to', 'valid_from', 'service_target'] as $column) {
                if (Schema::hasColumn('visa_vouchers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
