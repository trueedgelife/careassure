<?php

namespace App\Models;

use App\Support\ActingContext;
use App\Support\TenantContext;
use Spatie\Activitylog\Models\Activity as BaseActivity;

class AuditActivity extends BaseActivity
{
    protected static function booted(): void
    {
        static::creating(function (self $activity) {
            // Tenant from the active scope (web request, job, or runAs).
            $activity->tenant_id ??= app(TenantContext::class)->id();

            // No request object in console/queue contexts.
            $activity->ip_address ??= app()->runningInConsole()
                ? null
                : request()->ip();

            // Delegation context, if a delegate is acting on behalf of someone.
            $acting = app(ActingContext::class);
            $activity->acting_on_behalf_of ??= $acting->onBehalfOf();
            $activity->delegation_id ??= $acting->delegationId();
        });
    }
}
