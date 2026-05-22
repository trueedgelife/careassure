<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_package_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('care_package_id')->constrained('care_packages')->cascadeOnDelete();

            $table->date('review_date');
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_organisation')->nullable();
            $table->string('outcome')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('care_package_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_package_reviews');
    }
};
