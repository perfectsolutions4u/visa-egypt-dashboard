<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('visa_bookings') || ! Schema::hasTable('tours')) {
            return;
        }

        // Mobile Explore Egypt programs are tours. Drop invalid program_ids first.
        $tourIds = DB::table('tours')->pluck('id');
        DB::table('visa_bookings')
            ->whereNotNull('program_id')
            ->whereNotIn('program_id', $tourIds)
            ->update(['program_id' => null]);

        Schema::table('visa_bookings', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
        });

        Schema::table('visa_bookings', function (Blueprint $table) {
            $table->foreign('program_id')
                ->references('id')
                ->on('tours')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('visa_bookings') || ! Schema::hasTable('programs')) {
            return;
        }

        Schema::table('visa_bookings', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
        });

        Schema::table('visa_bookings', function (Blueprint $table) {
            $table->foreign('program_id')
                ->references('id')
                ->on('programs')
                ->nullOnDelete();
        });
    }
};
