<?php

namespace App\Support;

use App\Models\Tenant;

class TenantContext
{
    protected ?int $tenantId = null;
    protected bool $bypass = false;

    /**
     * The tenant ID to scope queries by, or null for no scoping.
     */
    public function id(): ?int
    {
        if ($this->bypass) {
            return null;
        }

        if ($this->tenantId !== null) {
            return $this->tenantId;
        }

        $user = auth()->user();
        if ($user && ! $user->is_super_admin && $user->tenant_id) {
            return $user->tenant_id;
        }

        return null;
    }

    public function set(int|Tenant $tenant): void
    {
        $this->tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;
    }

    public function clear(): void
    {
        $this->tenantId = null;
    }

    /**
     * Run a callback as a specific tenant. Use in jobs, console commands,
     * or super-admin actions that need to act inside a tenant boundary.
     */
    public function runAs(int|Tenant $tenant, callable $callback): mixed
    {
        $previousId = $this->tenantId;
        $previousBypass = $this->bypass;

        $this->bypass = false;
        $this->set($tenant);

        try {
            return $callback();
        } finally {
            $this->tenantId = $previousId;
            $this->bypass = $previousBypass;
        }
    }

    /**
     * Run a callback with tenant scoping disabled. Use sparingly —
     * this is the escape hatch for cross-tenant operations.
     */
    public function runWithoutScope(callable $callback): mixed
    {
        $previous = $this->bypass;
        $this->bypass = true;

        try {
            return $callback();
        } finally {
            $this->bypass = $previous;
        }
    }
}
