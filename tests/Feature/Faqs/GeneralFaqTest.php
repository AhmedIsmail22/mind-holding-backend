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

it('lists only published general faqs publicly', function () {
    Faq::factory()->create(['is_published' => true, 'order' => 1]);
    Faq::factory()->create(['is_published' => false, 'order' => 2]);

    $response = $this->getJson('/api/v1/public/faqs');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('does not leak service-linked faqs into the general public list', function () {
    $service = Service::factory()->create();
    $service->faqs()->create([
        'question' => ['ar' => 'س', 'en' => 'Q'],
        'answer' => ['ar' => 'ج', 'en' => 'A'],
        'is_published' => true,
        'order' => 0,
    ]);

    $response = $this->getJson('/api/v1/public/faqs');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);
});

it('allows a content editor to manage general faqs', function () {
    $editor = User::factory()->create();
    $editor->assignRole('Content editor');

    $response = $this->actingAs($editor, 'sanctum')->postJson('/api/v1/admin/faqs', [
        'question' => ['ar' => 'سؤال', 'en' => 'Question'],
        'answer' => ['ar' => 'إجابة', 'en' => 'Answer'],
        'is_published' => true,
        'order' => 1,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.question.en', 'Question');
});

it('validates faq creation', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/faqs', []);

    $response->assertStatus(422);
    expect($response->json('errors.question'))->not->toBeNull();
    expect($response->json('errors.answer'))->not->toBeNull();
});

it('soft deletes a general faq', function () {
    $faq = Faq::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/faqs/{$faq->id}");

    $response->assertOk();
    $this->assertSoftDeleted('faqs', ['id' => $faq->id]);
});

it('forbids sales from managing faqs', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this->actingAs($sales, 'sanctum')->getJson('/api/v1/admin/faqs')->assertStatus(403);
});
