<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carer_compliance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            // One-to-one: each carer has one compliance record.
            $table->foreignId('carer_id')->unique()->constrained('carers')->cascadeOnDelete();

            $table->string('dbs_certificate_number', 50)->nullable();
            $table->date('dbs_issued_on')->nullable();
            $table->boolean('dbs_on_update_service')->default(false);

            $table->date('right_to_work_verified_on')->nullable();
            $table->date('right_to_work_expires_on')->nullable();

            $table->date('references_completed_on')->nullable();

            $table->date('moving_handling_expires_on')->nullable();
            $table->date('safeguarding_expires_on')->nullable();
            $table->date('first_aid_expires_on')->nullable();
            $table->date('medication_expires_on')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carer_compliance');
    }
};
