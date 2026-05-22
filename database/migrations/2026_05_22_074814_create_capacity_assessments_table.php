<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capacity_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('service_user_id')->constrained('service_users')->cascadeOnDelete();

            // Free string, not an enum — decision domains vary widely
            // (financial, care_arrangements, medical, tenancy, ...).
            $table->string('decision_domain');
            $table->boolean('has_capacity');

            $table->string('assessed_by')->nullable();
            $table->date('assessed_on');
            $table->date('review_due')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('service_user_id');
            $table->index('review_due'); // for "assessments due for review" queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacity_assessments');
    }
};
