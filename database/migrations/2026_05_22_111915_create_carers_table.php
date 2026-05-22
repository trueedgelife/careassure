<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            // One profile maps to at most one carer record.
            $table->foreignId('profile_id')->unique()->constrained('profiles');

            $table->string('employment_type')->default('employee');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_primary_carer')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carers');
    }
};
