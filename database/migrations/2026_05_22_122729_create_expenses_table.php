<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('care_package_id')->constrained('care_packages');

            $table->string('category');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('supplier_name')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('care_package_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
