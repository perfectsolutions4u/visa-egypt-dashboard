<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('client_vouchers')) {
            return;
        }

        Schema::create('client_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('voucher_id')->constrained('visa_vouchers')->cascadeOnDelete();
            $table->timestamp('redeemed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['client_id', 'voucher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_vouchers');
    }
};
