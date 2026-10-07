<?php

use App\Models\Solution;
use App\Models\SolutionIndustry;
use App\Models\User;
use Database\Seeders\HomeContentSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(HomeContentSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

it('returns url, width, height and the current-locale alt for the hero image', function () {
    $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/home', [
        'hero_headline' => ['ar' => 'عنوان', 'en' => 'Headline'],
        'hero_subheadline' => ['ar' => 'فرعي', 'en' => 'Sub'],
        'differentiators' => [['title' => ['ar' => 'م', 'en' => 'T'], 'description' => ['ar' => 'م', 'en' => 'D']]],
        'process_steps' => [['title' => ['ar' => 'م', 'en' => 'S'], 'description' => ['ar' => 'م', 'en' => 'D'], 'duration' => ['ar' => 'م', 'en' => 'W']]],
        'closing_cta_headline' => ['ar' => 'م', 'en' => 'C'],
        'hero_image' => UploadedFile::fake()->image('hero.png', 1200, 800),
        'hero_image_alt' => ['ar' => 'صورة البطل', 'en' => 'Hero illustration'],
    ])->assertOk();

    $en = $this->getJson('/api/v1/public/home', ['Accept-Language' => 'en']);
    $image = $en->json('data.hero.image');

    expect($image['url'])->toContain('.webp');
    expect($image['width'])->toBeInt()->and($image['height'])->toBeInt();
    expect($image['alt'])->toBe('Hero illustration');

    $ar = $this->getJson('/api/v1/public/home', ['Accept-Language' => 'ar']);
    expect($ar->json('data.hero.image.alt'))->toBe('صورة البطل');
});

it('keeps the image and updates only the alt when no new file is sent', function () {
    $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/home', [
        'hero_headline' => ['ar' => 'م', 'en' => 'H'],
        'hero_subheadline' => ['ar' => 'م', 'en' => 'S'],
        'differentiators' => [['title' => ['ar' => 'م', 'en' => 'T'], 'description' => ['ar' => 'م', 'en' => 'D']]],
        'process_steps' => [['title' => ['ar' => 'م', 'en' => 'S'], 'description' => ['ar' => 'م', 'en' => 'D'], 'duration' => ['ar' => 'م', 'en' => 'W']]],
        'closing_cta_headline' => ['ar' => 'م', 'en' => 'C'],
        'hero_image' => UploadedFile::fake()->image('hero.png', 800, 600),
    ])->assertOk();
    $before = $this->getJson('/api/v1/public/home')->json('data.hero.image.url');

    $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/home', [
        'hero_headline' => ['ar' => 'م', 'en' => 'H'],
        'hero_subheadline' => ['ar' => 'م', 'en' => 'S'],
        'differentiators' => [['title' => ['ar' => 'م', 'en' => 'T'], 'description' => ['ar' => 'م', 'en' => 'D']]],
        'process_steps' => [['title' => ['ar' => 'م', 'en' => 'S'], 'description' => ['ar' => 'م', 'en' => 'D'], 'duration' => ['ar' => 'م', 'en' => 'W']]],
        'closing_cta_headline' => ['ar' => 'م', 'en' => 'C'],
        'hero_image_alt' => ['ar' => 'بديل', 'en' => 'New alt'],
    ])->assertOk();

    $after = $this->getJson('/api/v1/public/home', ['Accept-Language' => 'en'])->json('data.hero.image');
    expect($after['url'])->toBe($before);
    expect($after['alt'])->toBe('New alt');

    $admin = $this->actingAs($this->admin, 'sanctum')->getJson('/api/v1/admin/home')->json('data.hero_image_alt');
    expect($admin)->toBe(['ar' => 'بديل', 'en' => 'New alt']);
});

it('returns image objects with dimensions for solution thumbnails and gallery', function () {
    $industry = SolutionIndustry::factory()->create();
    $solution = Solution::factory()->create(['solution_industry_id' => $industry->id, 'is_published' => true, 'slug' => 'gallery-solution']);

    $this->actingAs($this->admin, 'sanctum')->post("/api/v1/admin/solutions/{$solution->id}", [
        ...solutionUpdatePayload($solution, $industry->id),
        'mockups' => [UploadedFile::fake()->image('a.png', 900, 600)],
        'mockups_alt' => ['ar' => 'لقطة', 'en' => 'Screenshot'],
    ])->assertOk();

    $detail = $this->getJson('/api/v1/public/solutions/gallery-solution', ['Accept-Language' => 'en'])->json('data');
    expect($detail['gallery'][0]['alt'])->toBe('Screenshot');
    expect($detail['gallery'][0]['width'])->toBeInt();

    $list = $this->getJson('/api/v1/public/solutions', ['Accept-Language' => 'en'])->json('data.0.thumbnail');
    expect($list['url'])->toContain('.webp');
    expect($list['height'])->toBeInt();
});

function solutionUpdatePayload(Solution $solution, int $industryId): array
{
    return [
        'solution_industry_id' => $industryId,
        'name' => ['ar' => 'حل', 'en' => 'Gallery solution'],
        'slug' => $solution->slug,
        'audience' => ['ar' => 'م', 'en' => 'Audience'],
        'problem_points' => [['ar' => 'م', 'en' => 'P']],
        'features' => ['customer' => [['ar' => 'م', 'en' => 'F']]],
        'deliverables' => [['ar' => 'م', 'en' => 'D']],
        'is_published' => true,
    ];
}
