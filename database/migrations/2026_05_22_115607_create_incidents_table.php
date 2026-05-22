<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('service_user_id')->constrained('service_users');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('severity');
            $table->string('incident_type', 100)->nullable();
            $table->text('description');
            $table->timestamp('occurred_at');
            $table->string('status')->default('open');
            $table->timestamp('safeguarding_referred_at')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('service_user_id');
            $table->index('status');
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
