<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('booking_code', 30)->unique();
            $table->string('status')->default('pending');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->time('expected_check_in_time')->nullable();
            $table->time('expected_check_out_time')->nullable();
            $table->timestamp('actual_check_in_at')->nullable();
            $table->timestamp('actual_check_out_at')->nullable();
            $table->string('source')->default('offline');
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->auditColumns();

            $table->index(['branch_id', 'status']);
            $table->index(['check_in_date', 'check_out_date']);
            $table->index('customer_id');
        });

        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_type_id')->constrained()->restrictOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->decimal('rate_snapshot', 15, 2);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedInteger('nights')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->auditColumns();

            $table->index('room_id');
            $table->index(['booking_id', 'room_id']);
            $table->index(['room_id', 'check_in_date', 'check_out_date']);
        });

        Schema::create('booking_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('id_number', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->auditColumns();

            $table->index('booking_id');
        });

        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamp('service_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->auditColumns();

            $table->index('booking_id');
        });

        Schema::create('booking_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->json('changes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->auditColumns();

            $table->index(['booking_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_histories');
        Schema::dropIfExists('booking_services');
        Schema::dropIfExists('booking_guests');
        Schema::dropIfExists('booking_rooms');
        Schema::dropIfExists('bookings');
    }
};
