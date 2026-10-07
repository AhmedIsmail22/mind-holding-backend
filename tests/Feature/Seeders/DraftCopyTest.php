<?php

use App\Models\HomeContent;
use App\Models\Service;
use App\Models\Solution;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('seeds the approved service summaries as draft copy', function () {
    $service = Service::where('slug', 'mobile-apps')->firstOrFail();

    expect($service->getTranslation('summary', 'en'))->toBe('Android and iOS apps, from first idea to store launch.');
    expect($service->getTranslation('summary', 'ar'))->toBe('تطبيقات أندرويد وiOS، من الفكرة حتى النشر على المتاجر.');
    expect(Service::where('is_draft', false)->count())->toBe(0);
    expect(Service::whereNull('summary')->count())->toBe(0);
});

it('seeds the four flagship audiences and summaries, leaving the rest as draft', function () {
    $erp = Solution::where('slug', 'integrated-erp-system')->firstOrFail();

    expect($erp->getTranslation('audience', 'en'))->toBe('Mid-size and trading companies');
    expect($erp->getTranslation('summary', 'en'))->toBe('Sales, purchasing, inventory and accounting in one system, with live reports.');
    expect(Solution::where('is_flagship', true)->whereNotNull('summary')->count())->toBe(4);
    expect(Solution::where('is_draft', false)->count())->toBe(0);
});

it('seeds six differentiators and five process steps in the approved order', function () {
    $content = HomeContent::firstOrFail();

    expect($content->differentiators)->toHaveCount(6);
    expect($content->differentiators[0]['title']['en'])->toBe('Software and marketing, one team');
    expect($content->differentiators[5]['title']['en'])->toBe('We stay after launch');

    expect($content->process_steps)->toHaveCount(5);
    expect($content->process_steps[3]['duration']['en'])->toBe('Depends on scope');
    expect($content->is_draft)->toBeTrue();
});

it('does not overwrite copy an admin has already published', function () {
    Service::where('slug', 'mobile-apps')->update(['summary' => ['ar' => 'منشور', 'en' => 'Published by admin'], 'is_draft' => false]);
    HomeContent::query()->update(['is_draft' => false, 'differentiators' => [['title' => ['ar' => 'م', 'en' => 'Published'], 'description' => ['ar' => 'م', 'en' => 'P']]]]);

    $this->seed(DatabaseSeeder::class);

    expect(Service::where('slug', 'mobile-apps')->firstOrFail()->getTranslation('summary', 'en'))->toBe('Published by admin');
    expect(HomeContent::firstOrFail()->differentiators)->toHaveCount(1);
});

it('is idempotent: a second run leaves a single admin and the same copy', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::where('email', 'admin@bitcodak.com')->count())->toBe(1);
    expect(Service::count())->toBe(8);
    expect(HomeContent::count())->toBe(1);
});
