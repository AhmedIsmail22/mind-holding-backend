<?php

use App\Models\SeoMeta;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SeoRouteSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(SeoRouteSeeder::class);

    $this->editor = User::factory()->create();
    $this->editor->assignRole('Content editor');
});

function validSeoPayload(array $overrides = []): array
{
    return array_merge([
        'title' => ['ar' => 'عنوان', 'en' => 'Custom title'],
        'description' => ['ar' => 'وصف', 'en' => 'Custom description'],
    ], $overrides);
}

it('serves the seeded SEO for a route page with a 200 and the locale content', function () {
    $response = $this->getJson('/api/v1/public/seo/home');

    $response->assertOk();
    expect($response->json('data.title'))->toBe('MIND Holding — Software & Marketing');
});

it('returns 404 for a route key that does not exist', function () {
    $this->getJson('/api/v1/public/seo/blog')->assertStatus(404);
});

it('lets a content editor save SEO for a route page with a share image converted to webp', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->editor, 'sanctum')->post('/api/v1/admin/seo/route/contact', [
        ...validSeoPayload(),
        'share_image' => UploadedFile::fake()->image('share.png', 1600, 900),
    ]);

    $response->assertOk();
    expect($response->json('data.is_draft'))->toBeFalse();
    expect($response->json('data.share_image_url'))->toContain('.webp');
});

it('falls back to the service name for a service with no SEO row', function () {
    $service = Service::factory()->create(['slug' => 'web-design', 'is_published' => true, 'name' => ['ar' => 'تصميم', 'en' => 'Web design']]);

    $response = $this->getJson('/api/v1/public/services/web-design', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.seo.title'))->toBe('Web design');
    expect($response->json('data.seo.share_image_url'))->toBeNull();
});

it('uses the saved SEO for a service when one exists', function () {
    $service = Service::factory()->create(['slug' => 'web-design', 'is_published' => true]);
    $this->actingAs($this->editor, 'sanctum')->post("/api/v1/admin/seo/service/{$service->id}", validSeoPayload())->assertOk();

    $response = $this->getJson('/api/v1/public/services/web-design', ['Accept-Language' => 'en']);

    expect($response->json('data.seo.title'))->toBe('Custom title');
    expect($response->json('data.seo.description'))->toBe('Custom description');
});

it('rejects a title longer than 70 characters', function () {
    $response = $this->actingAs($this->editor, 'sanctum')->postJson('/api/v1/admin/seo/route/home', validSeoPayload([
        'title' => ['ar' => 'ع', 'en' => str_repeat('x', 71)],
    ]));

    $response->assertStatus(422);
    expect($response->json('errors'))->toHaveKey('title.en');
});

it('returns a null-valued SEO shell for an admin target with no SEO yet', function () {
    $service = Service::factory()->create();

    $response = $this->actingAs($this->editor, 'sanctum')->getJson("/api/v1/admin/seo/service/{$service->id}");

    $response->assertOk();
    expect($response->json('data.title'))->toBe(['ar' => null, 'en' => null]);
    expect(SeoMeta::where('seoable_id', $service->id)->exists())->toBeFalse();
});

it('forbids sales from editing SEO', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->getJson('/api/v1/admin/seo/route/home')->assertStatus(403);
});
