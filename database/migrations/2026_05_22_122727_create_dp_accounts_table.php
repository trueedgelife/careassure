<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dp_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            // One account per care package.
            $table->foreignId('care_package_id')->unique()->constrained('care_packages');

            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->date('opened_on');
            $table->date('closed_on')->nullable();
            $table->string('bank_account_ref', 100)->nullable(); // masked at app layer

            $table->timestamps();

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dp_accounts');
    }
};
