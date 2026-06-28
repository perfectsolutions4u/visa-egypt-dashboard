<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'whatsapp')) {
                $table->string('whatsapp', 125)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('clients', 'language')) {
                $table->string('language', 10)->default('ar')->after('whatsapp');
            }
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 125);
            $table->string('slug', 125)->unique();
            $table->string('duration', 125)->nullable();
            $table->longText('cities')->nullable();
            $table->longText('highlights')->nullable();
            $table->longText('itinerary')->nullable();
            $table->longText('inclusions')->nullable();
            $table->longText('exclusions')->nullable();
            $table->decimal('starting_price', 12, 2)->default(0);
            $table->string('hero_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_best_seller')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('service_type', 50);
            $table->string('tier', 50);
            $table->string('name', 125);
            $table->decimal('price', 12, 2);
            $table->longText('features')->nullable();
            $table->boolean('includes_visa')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->unsignedTinyInteger('duration_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('name', 125);
            $table->unsignedTinyInteger('max_passengers')->default(3);
            $table->unsignedTinyInteger('max_bags')->default(2);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('full_name', 125);
            $table->string('phone', 125);
            $table->string('whatsapp', 125)->nullable();
            $table->longText('languages')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->unsignedInteger('reviews_count')->default(0);
            $table->string('photo')->nullable();
            $table->string('license_number', 125)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('visa_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('booking_ref', 20)->unique();
            $table->string('service_type', 50);
            $table->string('status', 50)->default('pending');
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('service_package_id')->nullable()->constrained('service_packages')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->date('travel_date')->nullable();
            $table->unsignedSmallInteger('travelers_count')->default(1);
            $table->string('nationality', 125)->nullable();
            $table->string('contact_email', 125)->nullable();
            $table->string('contact_whatsapp', 125)->nullable();
            $table->string('flight_number', 125)->nullable();
            $table->time('arrival_time')->nullable();
            $table->string('meeting_point', 125)->nullable();
            $table->string('destination', 125)->nullable();
            $table->longText('special_requests')->nullable();
            $table->longText('metadata')->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('visa_booking_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_booking_id')->constrained('visa_bookings')->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_booking_id')->constrained('visa_bookings')->cascadeOnDelete();
            $table->string('status_key', 125);
            $table->string('status_label', 125);
            $table->timestamp('event_at');
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->boolean('is_current')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('plan_type', 50);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->unsignedInteger('points_balance')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 50)->default('active');
            $table->timestamps();
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained('clients')->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('bonus_credit', 12, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->string('type', 50);
            $table->decimal('amount', 12, 2);
            $table->string('reference', 125)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title', 125);
            $table->text('description')->nullable();
            $table->string('service_target', 50);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->string('membership_level', 50)->nullable();
            $table->dateTime('active_from')->nullable();
            $table->dateTime('active_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->string('title', 125);
            $table->text('body')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('target_screen', 125)->nullable();
            $table->string('target_id', 125)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('visa_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('visa_booking_id')->nullable()->constrained('visa_bookings')->nullOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained('memberships')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('method', 50);
            $table->string('status', 50)->default('pending');
            $table->string('gateway_reference', 125)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_payments');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('tracking_events');
        Schema::dropIfExists('visa_booking_assignments');
        Schema::dropIfExists('visa_bookings');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('service_packages');
        Schema::dropIfExists('programs');

        $clientColumns = array_filter([
            Schema::hasColumn('clients', 'whatsapp') ? 'whatsapp' : null,
            Schema::hasColumn('clients', 'language') ? 'language' : null,
        ]);

        if ($clientColumns !== []) {
            Schema::table('clients', function (Blueprint $table) use ($clientColumns) {
                $table->dropColumn($clientColumns);
            });
        }
    }
};
