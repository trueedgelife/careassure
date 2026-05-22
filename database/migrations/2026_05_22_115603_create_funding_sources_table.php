<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('care_package_id')->constrained('care_packages')->cascadeOnDelete();

            $table->string('funder_type');
            $table->string('funder_name')->nullable();
            $table->decimal('weekly_amount', 10, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('reference', 100)->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['care_package_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_sources');
    }
};
