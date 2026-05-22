<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nullable: super admins (platform staff) have no tenant.
            $table->foreignId('tenant_id')->nullable()->after('id')
                ->constrained('tenants'); // RESTRICT on delete (the default)

            $table->boolean('is_super_admin')->default(false)->after('password');
            $table->boolean('is_active')->default(true)->after('is_super_admin');
            $table->timestamp('last_login_at')->nullable()->after('is_active');

            // Drop the global email unique, replace with tenant-scoped.
            $table->dropUnique(['email']);
            $table->unique(['tenant_id', 'email'], 'users_tenant_id_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_tenant_id_email_unique');
            $table->unique('email');
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn(['is_super_admin', 'is_active', 'last_login_at']);
        });
    }
};
