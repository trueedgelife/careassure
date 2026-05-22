<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('service_user_id')->constrained('service_users');

            $table->decimal('weekly_funded_hours', 6, 2)->nullable();
            // Denormalised cache; funding_sources is the source of truth.
            $table->decimal('annual_budget', 10, 2)->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('review_frequency_days')->default(365);

            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('service_user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_packages');
    }
};
