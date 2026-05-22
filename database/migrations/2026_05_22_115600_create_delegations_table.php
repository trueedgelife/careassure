<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delegations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('delegator_user_id')->constrained('users');
            $table->foreignId('delegate_user_id')->constrained('users');

            $table->json('scope'); // e.g. {"manage_shifts": true, "upload_expenses": true}

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['delegate_user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delegations');
    }
};
