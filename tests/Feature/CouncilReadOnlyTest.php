<?php

use App\Filament\Council\Resources\CarePackages\CarePackageResource;
use App\Filament\Council\Resources\DpAccounts\DpAccountResource;
use App\Filament\Council\Resources\Incidents\IncidentResource;
use App\Filament\Council\Resources\ServiceUsers\ServiceUserResource;

/**
 * The council panel is read-only oversight. Reviewers can view council-wide
 * data (the tenant scope limits them to their own council) but can never
 * create, edit, or delete. These checks lock that contract in.
 */

it('all council resources are read-only', function () {
    $resources = [
        ServiceUserResource::class,
        CarePackageResource::class,
        IncidentResource::class,
        DpAccountResource::class,
    ];

    foreach ($resources as $resource) {
        expect($resource::canCreate())->toBeFalse("{$resource} should not allow create");
        // canEdit / canDelete take a record; null is fine for the guard check.
        expect($resource::canEdit(new ($resource::getModel())))->toBeFalse("{$resource} should not allow edit");
        expect($resource::canDelete(new ($resource::getModel())))->toBeFalse("{$resource} should not allow delete");
    }
});

it('council resources expose only index and view pages (no create/edit routes)', function () {
    $resources = [
        ServiceUserResource::class,
        CarePackageResource::class,
        IncidentResource::class,
        DpAccountResource::class,
    ];

    foreach ($resources as $resource) {
        $pages = array_keys($resource::getPages());
        expect($pages)->toContain('index')
            ->and($pages)->toContain('view')
            ->and($pages)->not->toContain('create')
            ->and($pages)->not->toContain('edit');
    }
});
