<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dp_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('dp_account_id')->constrained('dp_accounts')->cascadeOnDelete();

            $table->string('direction');           // credit / debit
            $table->decimal('amount', 12, 2);       // always positive
            $table->date('transaction_date');
            $table->string('source_type');

            // Links back to whatever originated this entry (expense, shift, etc).
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();

            $table->timestamp('reconciled_at')->nullable();
            $table->string('bank_reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['dp_account_id', 'transaction_date']);
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dp_transactions');
    }
};
