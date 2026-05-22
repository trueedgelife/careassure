<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carer_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('carer_id')->constrained('carers')->cascadeOnDelete();

            $table->string('rate_type')->default('day');
            // hourly_rate for time-based pay; flat_rate for fixed sums (e.g. sleep-ins).
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('flat_rate', 8, 2)->nullable();

            $table->date('effective_from');
            $table->date('effective_to')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['carer_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carer_rates');
    }
};
