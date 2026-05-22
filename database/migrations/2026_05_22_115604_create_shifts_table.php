<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('care_package_id')->constrained('care_packages');
            $table->foreignId('carer_id')->constrained('carers');

            // Full datetimes — supports sleep-ins and midnight-crossing shifts.
            $table->dateTime('scheduled_start_at');
            $table->dateTime('scheduled_end_at');
            $table->dateTime('actual_start_at')->nullable();
            $table->dateTime('actual_end_at')->nullable();

            $table->integer('unpaid_break_minutes')->default(0);
            $table->string('support_category', 100)->nullable();
            $table->text('notes')->nullable();

            $table->string('status')->default('scheduled');

            // Snapshot costing — captured when the shift is costed, reflecting
            // the carer's rate AT THE TIME, not today's rate.
            $table->foreignId('carer_rate_id')->nullable()->constrained('carer_rates')->nullOnDelete();
            $table->decimal('computed_cost', 10, 2)->nullable();
            $table->timestamp('costed_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['carer_id', 'scheduled_start_at']);
            $table->index(['care_package_id', 'scheduled_start_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
