<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('service_user_id')->constrained('service_users')->cascadeOnDelete();
            // The related person (daughter, GP, etc). May have no login.
            $table->foreignId('related_profile_id')->constrained('profiles');

            $table->string('relationship_type');
            $table->boolean('is_emergency_contact')->default(false);
            $table->boolean('has_lpa')->default(false);
            $table->string('lpa_type')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('service_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_relationships');
    }
};
