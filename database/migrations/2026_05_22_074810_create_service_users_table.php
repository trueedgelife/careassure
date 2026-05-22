<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            // One profile maps to at most one service-user record.
            $table->foreignId('profile_id')->unique()->constrained('profiles');

            $table->string('nhs_number', 20)->nullable();
            $table->string('council_reference', 100)->nullable();
            $table->date('funding_start_date')->nullable();

            $table->string('status')->default('assessment');
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_users');
    }
};
