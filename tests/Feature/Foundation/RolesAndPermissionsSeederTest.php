<?php

use Database\Seeders\RolesAndPermissionsSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('creates the three roles from the srs', function () {
    expect(Role::pluck('name')->sort()->values()->all())
        ->toBe(['Administrator', 'Content editor', 'Sales']);
});

it('gives the administrator every permission', function () {
    $administrator = Role::findByName('Administrator');

    expect($administrator->permissions()->count())->toBeGreaterThanOrEqual(14);
    expect($administrator->hasPermissionTo('users.manage'))->toBeTrue();
    expect($administrator->hasPermissionTo('settings.manage'))->toBeTrue();
    expect($administrator->hasPermissionTo('solutions.manage'))->toBeTrue();
    expect($administrator->hasPermissionTo('requests.view'))->toBeTrue();
});

it('restricts the content editor to content permissions only', function () {
    $contentEditor = Role::findByName('Content editor');

    expect($contentEditor->hasPermissionTo('solutions.manage'))->toBeTrue();
    expect($contentEditor->hasPermissionTo('services.manage'))->toBeTrue();
    expect($contentEditor->hasPermissionTo('work.manage'))->toBeTrue();
    expect($contentEditor->hasPermissionTo('faqs.manage'))->toBeTrue();

    expect($contentEditor->hasPermissionTo('settings.manage'))->toBeFalse();
    expect($contentEditor->hasPermissionTo('users.manage'))->toBeFalse();
    expect($contentEditor->hasPermissionTo('requests.view'))->toBeFalse();
});

it('restricts sales to viewing and updating request status only', function () {
    $sales = Role::findByName('Sales');

    expect($sales->hasPermissionTo('requests.view'))->toBeTrue();
    expect($sales->hasPermissionTo('requests.manage-status'))->toBeTrue();

    expect($sales->hasPermissionTo('solutions.manage'))->toBeFalse();
    expect($sales->hasPermissionTo('settings.manage'))->toBeFalse();
    expect($sales->hasPermissionTo('users.manage'))->toBeFalse();
});
