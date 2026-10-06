<?php

use App\Models\HomeSection;
use App\Models\User;
use Database\Seeders\HomeContentSeeder;
use Database\Seeders\HomeSectionsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(HomeContentSeeder::class);
    $this->seed(HomeSectionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

function validHomeContentPayload(array $overrides = []): array
{
    return array_merge([
        'hero_headline' => ['ar' => 'عنوان', 'en' => 'Headline'],
        'hero_subheadline' => ['ar' => 'فرعي', 'en' => 'Subheadline'],
        'stats' => [],
        'differentiators' => [
            ['title' => ['ar' => 'عنوان', 'en' => 'Title'], 'description' => ['ar' => 'وصف', 'en' => 'Description']],
        ],
        'process_steps' => [
            ['title' => ['ar' => 'خطوة', 'en' => 'Step'], 'description' => ['ar' => 'وصف', 'en' => 'Desc'], 'duration' => ['ar' => 'أسبوع', 'en' => 'Week 1']],
        ],
        'closing_cta_headline' => ['ar' => 'ختام', 'en' => 'Closing'],
    ], $overrides);
}

it('returns home content in the resolved locale with empty stats by default', function () {
    $response = $this->getJson('/api/v1/public/home', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.hero.headline'))->toBe('We combine software and marketing to grow your business');
    expect($response->json('data.differentiators'))->toHaveCount(3);
    expect($response->json('data.process_steps'))->toHaveCount(5);
    expect($response->json('data.stats'))->toBe([]);
});

it('allows an administrator to update home content and clears the draft flag', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/home', validHomeContentPayload());

    $response->assertOk();
    $response->assertJsonPath('data.hero_headline.en', 'Headline');
    $response->assertJsonPath('data.is_draft', false);
});

it('uploads a hero image converted to webp', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/home', [
        ...validHomeContentPayload(),
        'hero_image' => UploadedFile::fake()->image('hero.png', 800, 600),
    ]);

    $response->assertOk();
    expect($response->json('data.hero_image_url'))->toContain('.webp');
});

it('validates home content input', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/home', []);

    $response->assertStatus(422);
    expect($response->json('errors.hero_headline'))->not->toBeNull();
    expect($response->json('errors.differentiators'))->not->toBeNull();
    expect($response->json('errors.process_steps'))->not->toBeNull();
});

it('rejects more than four stats', function () {
    $stat = ['label' => ['ar' => 'س', 'en' => 'S'], 'value' => '10'];

    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/home', validHomeContentPayload([
        'stats' => [$stat, $stat, $stat, $stat, $stat],
    ]));

    $response->assertStatus(422);
    expect($response->json('errors.stats'))->not->toBeNull();
});

it('lists only enabled home sections in order publicly', function () {
    HomeSection::where('key', 'quick_stats')->update(['is_enabled' => false]);

    $response = $this->getJson('/api/v1/public/home/sections');

    $response->assertOk();
    $keys = collect($response->json('data'))->pluck('key')->all();
    expect($keys)->not->toContain('quick_stats');
    expect($keys[0])->toBe('hero');
    expect(count($keys))->toBe(9);
});

it('allows an administrator to bulk update sections', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/home/sections', [
        'sections' => [
            ['key' => 'faq', 'is_enabled' => false, 'order' => 0],
            ['key' => 'hero', 'is_enabled' => true, 'order' => 1],
        ],
    ]);

    $response->assertOk();
    expect(HomeSection::where('key', 'faq')->first()->is_enabled)->toBeFalse();
    expect(HomeSection::where('key', 'hero')->first()->order)->toBe(1);
});

it('rejects an unknown section key', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/home/sections', [
        'sections' => [['key' => 'made_up', 'is_enabled' => true, 'order' => 0]],
    ]);

    $response->assertStatus(422);
    expect($response->json('errors'))->toHaveKey('sections.0.key');
});

it('lets a content editor manage home content', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $this->actingAs($editor, 'sanctum')->postJson('/api/v1/admin/home', validHomeContentPayload())->assertOk();
});

it('forbids sales from managing home content and sections', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->getJson('/api/v1/admin/home')->assertStatus(403);
    $this->actingAs($sales, 'sanctum')->getJson('/api/v1/admin/home/sections')->assertStatus(403);
});
