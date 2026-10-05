<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Permissions a "Content editor" holds, per SRS §6 roles table: Services,
     * solutions, work, pages, FAQs, media. SEO fields live on those same
     * records, so SEO editing travels with them.
     */
    private const CONTENT_EDITOR_PERMISSIONS = [
        'solution-industries.manage',
        'solutions.manage',
        'services.manage',
        'work.manage',
        'pages.manage',
        'home-content.manage',
        'faqs.manage',
        'media.manage',
        'seo.manage',
    ];

    /**
     * Permissions a "Sales" user holds: view requests and change their
     * status only (SRS §6).
     */
    private const SALES_PERMISSIONS = [
        'requests.view',
        'requests.manage-status',
    ];

    /**
     * Administrator-only permissions not granted to Content editor or Sales:
     * users, settings, and legacy-URL redirects (infra, not content).
     */
    private const ADMIN_ONLY_PERMISSIONS = [
        'users.manage',
        'settings.manage',
        'redirects.manage',
    ];

    public function run(): void
    {
        $allPermissions = [
            ...self::CONTENT_EDITOR_PERMISSIONS,
            ...self::SALES_PERMISSIONS,
            ...self::ADMIN_ONLY_PERMISSIONS,
        ];

        foreach ($allPermissions as $permission) {
            Permission::findOrCreate($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $administrator = Role::findOrCreate('Administrator');
        $administrator->syncPermissions($allPermissions);

        $contentEditor = Role::findOrCreate('Content editor');
        $contentEditor->syncPermissions(self::CONTENT_EDITOR_PERMISSIONS);

        $sales = Role::findOrCreate('Sales');
        $sales->syncPermissions(self::SALES_PERMISSIONS);
    }
}
