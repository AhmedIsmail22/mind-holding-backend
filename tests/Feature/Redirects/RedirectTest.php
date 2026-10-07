<?php

use App\Models\Redirect;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
    $this->editor = User::factory()->create();
    $this->editor->assignRole('Content editor');
});

it('lets an administrator create a redirect that resolves with a 301', function () {
    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/old-services/web-design/',
        'new_path' => '/services/website-design-development',
    ])->assertCreated();

    $response = $this->getJson('/api/v1/public/redirects/resolve?path=/old-services/web-design');

    $response->assertOk();
    expect($response->json('data'))->toBe([
        'from' => '/old-services/web-design',
        'to' => '/services/website-design-development',
        'status' => 301,
    ]);
});

it('returns 404 when no redirect matches the path', function () {
    $this->getJson('/api/v1/public/redirects/resolve?path=/nothing-here')->assertStatus(404);
});

it('requires a path to resolve', function () {
    $this->getJson('/api/v1/public/redirects/resolve')->assertStatus(422);
});

it('rejects a duplicate active old path', function () {
    Redirect::create(['old_path' => '/a', 'new_path' => '/b']);

    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/a',
        'new_path' => '/c',
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.old_path'))->not->toBeNull();
});

it('rejects a redirect that points to itself', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/same',
        'new_path' => '/same',
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.new_path'))->not->toBeNull();
});

it('rejects a full URL instead of a path', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => 'https://mindholding.net/old',
        'new_path' => '/new',
    ]);

    $response->assertStatus(422);
});

it('stops resolving a soft-deleted redirect and allows its path to be reused', function () {
    $redirect = Redirect::create(['old_path' => '/gone', 'new_path' => '/old-target']);

    $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/redirects/{$redirect->id}")->assertOk();
    $this->getJson('/api/v1/public/redirects/resolve?path=/gone')->assertStatus(404);

    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/gone',
        'new_path' => '/new-target',
    ])->assertCreated();
});

it('lists active redirects publicly', function () {
    Redirect::create(['old_path' => '/one', 'new_path' => '/uno']);
    Redirect::create(['old_path' => '/two', 'new_path' => '/dos']);

    $response = $this->getJson('/api/v1/public/redirects');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.status'))->toBe(301);
});

it('forbids a content editor from managing redirects', function () {
    $this->actingAs($this->editor, 'sanctum')->getJson('/api/v1/admin/redirects')->assertStatus(403);
});

it('does not call the frontend when no revalidation URL is configured', function () {
    config(['services.frontend.revalidate_url' => null]);
    Http::fake();

    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/old',
        'new_path' => '/new',
    ])->assertCreated();

    Http::assertNothingSent();
});

it('notifies the frontend when a redirect is created', function () {
    config(['services.frontend.revalidate_url' => 'https://frontend.example/api/revalidate']);
    Http::fake();

    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/old',
        'new_path' => '/new',
    ])->assertCreated();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://frontend.example/api/revalidate'
            && $request['event'] === 'created'
            && $request['redirect'] === ['old_path' => '/old', 'new_path' => '/new']
            && $request['previous'] === null;
    });
});

it('notifies the frontend with both paths when a redirect is updated', function () {
    config(['services.frontend.revalidate_url' => 'https://frontend.example/api/revalidate']);
    $redirect = Redirect::create(['old_path' => '/old', 'new_path' => '/new']);
    Http::fake();

    $this->actingAs($this->admin, 'sanctum')->putJson("/api/v1/admin/redirects/{$redirect->id}", [
        'old_path' => '/old-2',
        'new_path' => '/new-2',
    ])->assertOk();

    Http::assertSent(function ($request) {
        return $request['event'] === 'updated'
            && $request['redirect'] === ['old_path' => '/old-2', 'new_path' => '/new-2']
            && $request['previous'] === ['old_path' => '/old', 'new_path' => '/new'];
    });
});

it('notifies the frontend when a redirect is deleted', function () {
    config(['services.frontend.revalidate_url' => 'https://frontend.example/api/revalidate']);
    $redirect = Redirect::create(['old_path' => '/gone', 'new_path' => '/target']);
    Http::fake();

    $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/redirects/{$redirect->id}")->assertOk();

    Http::assertSent(function ($request) {
        return $request['event'] === 'deleted'
            && $request['redirect'] === ['old_path' => '/gone', 'new_path' => '/target'];
    });
});

it('still saves the redirect even if the frontend revalidation call fails', function () {
    config(['services.frontend.revalidate_url' => 'https://frontend.example/api/revalidate']);
    Http::fake(['frontend.example/*' => Http::response('', 500)]);

    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/redirects', [
        'old_path' => '/old',
        'new_path' => '/new',
    ])->assertCreated();

    expect(Redirect::where('old_path', '/old')->exists())->toBeTrue();
});
