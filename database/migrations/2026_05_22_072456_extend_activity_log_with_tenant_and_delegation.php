<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Which tenant this event belongs to. Nullable: platform-level
            // (super admin / CLI) actions have no tenant.
            $table->foreignId('tenant_id')->nullable()->after('id')
                ->constrained('tenants')->nullOnDelete();

            // When a delegate acts for a service user, this records WHOSE
            // authority was used — distinct from causer_id (who clicked).
            $table->foreignId('acting_on_behalf_of')->nullable()
                ->constrained('users')->nullOnDelete();

            // Which delegation grant authorised the action. No FK yet —
            // the delegations table arrives in layer 7; FK added then.
            $table->unsignedBigInteger('delegation_id')->nullable();

            $table->string('ip_address', 45)->nullable();

            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'created_at']);
            $table->dropConstrainedForeignId('acting_on_behalf_of');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn(['delegation_id', 'ip_address']);
        });
    }
};
