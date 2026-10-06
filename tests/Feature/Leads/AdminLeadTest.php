<?php

use App\Contracts\RecaptchaVerifier;
use App\Models\Lead;
use App\Models\Service;
use App\Models\Solution;
use App\Models\SolutionIndustry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Support\Facades\Mail;
use Tests\Support\FakeRecaptchaVerifier;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(SettingsSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrator');

    $this->sales = User::factory()->create();
    $this->sales->assignRole('Sales');

    $this->editor = User::factory()->create();
    $this->editor->assignRole('Content editor');

    $this->lead = Lead::factory()->create(['type' => 'quote']);
});

it('lists leads for sales with the call and WhatsApp links', function () {
    $response = $this->actingAs($this->sales, 'sanctum')->getJson('/api/v1/admin/leads');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.call_url'))->toBe('tel:+201115646730');
    expect($response->json('data.0.whatsapp_url'))->toBe('https://wa.me/201115646730');
    expect($response->json('meta.total'))->toBe(1);
});

it('filters leads by type and status', function () {
    Lead::factory()->create(['type' => 'callback', 'status' => 'contacted']);

    $this->actingAs($this->sales, 'sanctum')->getJson('/api/v1/admin/leads?type=callback')
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $this->actingAs($this->sales, 'sanctum')->getJson('/api/v1/admin/leads?status=contacted')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('rejects an invalid filter value', function () {
    $response = $this->actingAs($this->sales, 'sanctum')->getJson('/api/v1/admin/leads?status=bogus');

    $response->assertStatus(422);
    expect($response->json('errors.status'))->not->toBeNull();
});

it('lets sales change a lead status', function () {
    $response = $this->actingAs($this->sales, 'sanctum')
        ->patchJson("/api/v1/admin/leads/{$this->lead->id}/status", ['status' => 'won']);

    $response->assertOk();
    expect($this->lead->refresh()->status)->toBe('won');
});

it('rejects an unknown status value', function () {
    $response = $this->actingAs($this->sales, 'sanctum')
        ->patchJson("/api/v1/admin/leads/{$this->lead->id}/status", ['status' => 'closed']);

    $response->assertStatus(422);
});

it('lets an administrator assign a lead to a user', function () {
    $response = $this->actingAs($this->admin, 'sanctum')
        ->patchJson("/api/v1/admin/leads/{$this->lead->id}/assign", ['user_id' => $this->sales->id]);

    $response->assertOk();
    expect($this->lead->refresh()->assigned_to)->toBe($this->sales->id);
});

it('lets an administrator soft delete a lead', function () {
    $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/leads/{$this->lead->id}");

    $response->assertOk();
    $this->assertSoftDeleted('leads', ['id' => $this->lead->id]);
});

it('forbids sales from assigning or deleting leads', function () {
    $this->actingAs($this->sales, 'sanctum')
        ->patchJson("/api/v1/admin/leads/{$this->lead->id}/assign", ['user_id' => $this->sales->id])
        ->assertStatus(403);

    $this->actingAs($this->sales, 'sanctum')
        ->deleteJson("/api/v1/admin/leads/{$this->lead->id}")
        ->assertStatus(403);
});

it('forbids content editors from viewing leads', function () {
    $this->actingAs($this->editor, 'sanctum')->getJson('/api/v1/admin/leads')->assertStatus(403);
    $this->actingAs($this->editor, 'sanctum')->getJson('/api/v1/admin/leads/dashboard')->assertStatus(403);
});

it('reports counts and the most requested solutions and services on the dashboard', function () {
    $industry = SolutionIndustry::factory()->create();
    $solution = Solution::factory()->create(['solution_industry_id' => $industry->id]);
    $service = Service::factory()->create();

    Lead::factory()->count(2)->create(['type' => 'demo', 'solution_id' => $solution->id]);
    Lead::factory()->create(['type' => 'quote', 'service_id' => $service->id, 'status' => 'spam']);

    $response = $this->actingAs($this->sales, 'sanctum')->getJson('/api/v1/admin/leads/dashboard');

    $response->assertOk();
    expect($response->json('data.counts.today'))->toBe(4);
    expect($response->json('data.top_solutions.0.requests'))->toBe(2);
    expect($response->json('data.top_services'))->toBe([]);
    expect($response->json('data.recent'))->toHaveCount(4);
});

it('still saves the lead when the notification email fails', function () {
    $this->app->instance(RecaptchaVerifier::class, new FakeRecaptchaVerifier(true));
    Mail::shouldReceive('to')->andThrow(new RuntimeException('smtp down'));

    $response = $this->postJson('/api/v1/public/leads/callback', [
        'name' => 'Ahmed',
        'mobile' => '+20 111 564 6730',
        'page_url' => 'https://mindholding.net/ar',
        'recaptcha_token' => 'token',
    ]);

    $response->assertCreated();
    expect(Lead::where('name', 'Ahmed')->exists())->toBeTrue();
});
