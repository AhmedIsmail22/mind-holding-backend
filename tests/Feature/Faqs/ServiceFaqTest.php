<?php

use App\Models\Faq;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

it('shows service-specific faqs on the public service detail endpoint', function () {
    $service = Service::factory()->create(['slug' => 'website-design', 'is_published' => true]);
    $service->faqs()->create([
        'question' => ['ar' => 'س', 'en' => 'How long does it take?'],
        'answer' => ['ar' => 'ج', 'en' => 'A few weeks.'],
        'is_published' => true,
        'order' => 0,
    ]);

    $response = $this->getJson('/api/v1/public/services/website-design', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.faqs.0.question'))->toBe('How long does it take?');
});

it('allows an administrator to add a faq nested under a service', function () {
    $service = Service::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->postJson("/api/v1/admin/services/{$service->id}/faqs", [
        'question' => ['ar' => 'سؤال', 'en' => 'Question'],
        'answer' => ['ar' => 'إجابة', 'en' => 'Answer'],
        'is_published' => true,
        'order' => 0,
    ]);

    $response->assertCreated();

    $faq = Faq::first();
    expect($faq->faqable_type)->toBe(Service::class);
    expect($faq->faqable_id)->toBe($service->id);
});

it('lists only the faqs belonging to the given service', function () {
    $serviceA = Service::factory()->create();
    $serviceB = Service::factory()->create();

    $serviceA->faqs()->create(['question' => ['ar' => 'أ', 'en' => 'A'], 'answer' => ['ar' => 'أ', 'en' => 'A'], 'order' => 0]);
    $serviceB->faqs()->create(['question' => ['ar' => 'ب', 'en' => 'B'], 'answer' => ['ar' => 'ب', 'en' => 'B'], 'order' => 0]);

    $response = $this->actingAs($this->admin, 'sanctum')->getJson("/api/v1/admin/services/{$serviceA->id}/faqs");

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.question.en'))->toBe('A');
});

it('rejects accessing a faq through the wrong service', function () {
    $serviceA = Service::factory()->create();
    $serviceB = Service::factory()->create();

    $faq = $serviceA->faqs()->create(['question' => ['ar' => 'أ', 'en' => 'A'], 'answer' => ['ar' => 'أ', 'en' => 'A'], 'order' => 0]);

    $response = $this->actingAs($this->admin, 'sanctum')->getJson("/api/v1/admin/services/{$serviceB->id}/faqs/{$faq->id}");

    $response->assertStatus(404);
});

it('forbids a sales user from managing service faqs', function () {
    $service = Service::factory()->create();
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->getJson("/api/v1/admin/services/{$service->id}/faqs")->assertStatus(403);
});
