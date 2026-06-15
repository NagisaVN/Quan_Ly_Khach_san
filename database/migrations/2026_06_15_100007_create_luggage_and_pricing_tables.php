<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luggage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tag_code', 30)->unique();
            $table->string('description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('storage_location')->nullable();
            $table->string('status')->default('stored');
            $table->timestamp('stored_at')->nullable();
            $table->timestamp('retrieved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->auditColumns();

            $table->index(['branch_id', 'status']);
            $table->index('customer_id');
        });

        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type');
            $table->json('conditions')->nullable();
            $table->string('adjustment_type')->default('percent');
            $table->decimal('value', 10, 2);
            $table->unsignedSmallInteger('priority')->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->auditColumns();

            $table->index(['branch_id', 'is_active', 'priority']);
        });

        Schema::create('seasonal_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rate', 15, 2);
            $table->decimal('adjustment_percent', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->auditColumns();

            $table->index(['branch_id', 'room_type_id', 'start_date', 'end_date']);
        });

        Schema::create('event_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->date('event_date');
            $table->decimal('rate', 15, 2)->nullable();
            $table->decimal('adjustment_percent', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->auditColumns();

            $table->index(['branch_id', 'event_date']);
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('date');
            $table->boolean('is_recurring')->default(false);
            $table->decimal('rate_multiplier', 5, 2)->default(1.00);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->auditColumns();

            $table->index(['company_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('event_rates');
        Schema::dropIfExists('seasonal_rates');
        Schema::dropIfExists('pricing_rules');
        Schema::dropIfExists('luggage');
    }
};
