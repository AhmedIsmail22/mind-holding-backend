<?php

use App\Models\Page;
use App\Models\Project;
use App\Models\Service;
use App\Models\Solution;
use App\Models\SolutionIndustry;
use App\Models\User;
use Database\Seeders\PagesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

it('falls back to the English slug for the Arabic alternate when no Arabic slug is set', function () {
    Service::factory()->create(['slug' => 'web-design', 'slug_ar' => null, 'is_published' => true]);

    $response = $this->getJson('/api/v1/public/services/web-design');

    expect($response->json('data.alternates'))->toBe(['ar' => 'web-design', 'en' => 'web-design']);
});

it('returns both alternates and resolves the Arabic slug', function () {
    Service::factory()->create(['slug' => 'web-design', 'slug_ar' => 'تصميم-المواقع', 'is_published' => true]);

    $ar = $this->getJson('/api/v1/public/services/'.rawurlencode('تصميم-المواقع'), ['Accept-Language' => 'ar']);
    $ar->assertOk();
    expect($ar->json('data.alternates'))->toBe(['ar' => 'تصميم-المواقع', 'en' => 'web-design']);

    $this->getJson('/api/v1/public/services/web-design')->assertOk();
});

it('returns alternates for solutions and their industries', function () {
    $industry = SolutionIndustry::factory()->create(['slug' => 'restaurants', 'slug_ar' => 'مطاعم']);
    Solution::factory()->create([
        'solution_industry_id' => $industry->id,
        'slug' => 'restaurant-app',
        'slug_ar' => null,
        'is_published' => true,
    ]);

    $response = $this->getJson('/api/v1/public/solutions/restaurant-app');

    expect($response->json('data.alternates'))->toBe(['ar' => 'restaurant-app', 'en' => 'restaurant-app']);
    expect($response->json('data.industry.alternates'))->toBe(['ar' => 'مطاعم', 'en' => 'restaurants']);
});

it('filters solutions by an Arabic industry slug', function () {
    $industry = SolutionIndustry::factory()->create(['slug' => 'logistics', 'slug_ar' => 'لوجستيات']);
    Solution::factory()->create(['solution_industry_id' => $industry->id, 'is_published' => true]);

    $this->getJson('/api/v1/public/solutions?industry='.rawurlencode('لوجستيات'))
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('returns industry alternates in the public list', function () {
    SolutionIndustry::factory()->create(['slug' => 'education', 'slug_ar' => null]);

    $response = $this->getJson('/api/v1/public/solution-industries');

    expect($response->json('data.0.alternates'))->toBe(['ar' => 'education', 'en' => 'education']);
});

it('returns project alternates from the slug and falls back to it for Arabic', function () {
    Project::factory()->create(['is_published' => true, 'slug' => 'home-goods-store', 'slug_ar' => null]);

    expect($this->getJson('/api/v1/public/projects')->json('data.0.alternates'))->toBe(['ar' => 'home-goods-store', 'en' => 'home-goods-store']);

    Project::query()->update(['slug_ar' => 'متجر-منزلي']);
    $response = $this->getJson('/api/v1/public/projects/home-goods-store');
    expect($response->json('data.alternates'))->toBe(['ar' => 'متجر-منزلي', 'en' => 'home-goods-store']);
});

it('returns page alternates and resolves a page by its Arabic slug', function () {
    $this->seed(PagesSeeder::class);
    Page::where('slug', 'about')->update(['slug_ar' => 'من-نحن']);

    $response = $this->getJson('/api/v1/public/pages/about');
    expect($response->json('data.alternates'))->toBe(['ar' => 'من-نحن', 'en' => 'about']);

    $this->getJson('/api/v1/public/pages/'.rawurlencode('من-نحن'))->assertOk();
    $this->getJson('/api/v1/public/pages/unknown')->assertStatus(404);
});

it('stores an Arabic slug on write and rejects a duplicate one', function () {
    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/services', [
        'group' => 'software',
        'name' => ['ar' => 'تصميم', 'en' => 'Design'],
        'slug' => 'design',
        'slug_ar' => 'تصميم',
        'description' => ['ar' => 'وصف', 'en' => 'Description'],
        'deliverables' => [['ar' => 'عنصر', 'en' => 'Item']],
        'delivery_steps' => [['ar' => 'خطوة', 'en' => 'Step']],
        'is_published' => true,
    ])->assertCreated();

    $this->assertDatabaseHas('services', ['slug' => 'design', 'slug_ar' => 'تصميم']);

    $duplicate = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/services', [
        'group' => 'marketing',
        'name' => ['ar' => 'تسويق', 'en' => 'Marketing'],
        'slug' => 'marketing',
        'slug_ar' => 'تصميم',
        'description' => ['ar' => 'وصف', 'en' => 'Description'],
        'deliverables' => [['ar' => 'عنصر', 'en' => 'Item']],
        'delivery_steps' => [['ar' => 'خطوة', 'en' => 'Step']],
    ]);

    $duplicate->assertStatus(422);
    expect($duplicate->json('errors'))->toHaveKey('slug_ar');
});
