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
                if (! Schema::hasColumn('visa_payments', 'subtotal')) {
                    $table->decimal('subtotal', 12, 2)->nullable()->after('membership_id');
                }
                if (! Schema::hasColumn('visa_payments', 'loyalty_discount')) {
                    $table->decimal('loyalty_discount', 12, 2)->default(0)->after('subtotal');
                }
                if (! Schema::hasColumn('visa_payments', 'points_used')) {
                    $table->unsignedInteger('points_used')->default(0)->after('loyalty_discount');
                }
                if (! Schema::hasColumn('visa_payments', 'points_earned')) {
                    $table->unsignedInteger('points_earned')->default(0)->after('points_used');
                }
            });
        }

        if (! Schema::hasTable('points_transactions')) {
            Schema::create('points_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('membership_id')->nullable()->constrained('memberships')->nullOnDelete();
                $table->foreignId('visa_payment_id')->nullable()->constrained('visa_payments')->nullOnDelete();
                $table->string('type', 20);
                $table->integer('points');
                $table->decimal('amount_usd', 12, 2)->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('points_transactions', 'amount_usd')) {
            Schema::table('points_transactions', function (Blueprint $table) {
                $table->decimal('amount_usd', 12, 2)->nullable()->after('points');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('points_transactions');

        if (Schema::hasTable('visa_payments')) {
            Schema::table('visa_payments', function (Blueprint $table) {
                $columns = ['subtotal', 'loyalty_discount', 'points_used', 'points_earned'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('visa_payments', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
