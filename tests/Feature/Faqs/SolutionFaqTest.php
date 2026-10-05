<?php

use App\Models\Faq;
use App\Models\Solution;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');
});

it('shows solution-specific faqs on the public solution detail endpoint', function () {
    $solution = Solution::factory()->create(['slug' => 'ecommerce-store', 'is_published' => true]);
    $solution->faqs()->create([
        'question' => ['ar' => 'س', 'en' => 'How does delivery work?'],
        'answer' => ['ar' => 'ج', 'en' => 'Through our delivery partners.'],
        'is_published' => true,
        'order' => 0,
    ]);

    $response = $this->getJson('/api/v1/public/solutions/ecommerce-store', ['Accept-Language' => 'en']);

    $response->assertOk();
    expect($response->json('data.faqs.0.question'))->toBe('How does delivery work?');
});

it('allows an administrator to add a faq nested under a solution', function () {
    $solution = Solution::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->postJson("/api/v1/admin/solutions/{$solution->id}/faqs", [
        'question' => ['ar' => 'سؤال', 'en' => 'Question'],
        'answer' => ['ar' => 'إجابة', 'en' => 'Answer'],
        'is_published' => true,
        'order' => 0,
    ]);

    $response->assertCreated();

    $faq = Faq::first();
    expect($faq->faqable_type)->toBe(Solution::class);
    expect($faq->faqable_id)->toBe($solution->id);
});

it('rejects accessing a faq through the wrong solution', function () {
    $solutionA = Solution::factory()->create();
    $solutionB = Solution::factory()->create();

    $faq = $solutionA->faqs()->create(['question' => ['ar' => 'أ', 'en' => 'A'], 'answer' => ['ar' => 'أ', 'en' => 'A'], 'order' => 0]);

    $response = $this->actingAs($this->admin, 'sanctum')->getJson("/api/v1/admin/solutions/{$solutionB->id}/faqs/{$faq->id}");

    $response->assertStatus(404);
});

it('forbids sales from managing solution faqs', function () {
    $solution = Solution::factory()->create();
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->getJson("/api/v1/admin/solutions/{$solution->id}/faqs")->assertStatus(403);
});
