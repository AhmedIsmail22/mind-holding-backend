<?php

use App\Contracts\RecaptchaVerifier;
use App\Mail\NewLeadMail;
use App\Models\Lead;
use App\Models\Service;
use App\Models\Solution;
use App\Models\SolutionIndustry;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Support\Facades\Mail;
use Tests\Support\FakeRecaptchaVerifier;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(SettingsSeeder::class);
    $this->app->instance(RecaptchaVerifier::class, new FakeRecaptchaVerifier(true));
    Mail::fake();

    $this->service = Service::factory()->create();
    $this->solution = Solution::factory()->create([
        'solution_industry_id' => SolutionIndustry::factory()->create()->id,
    ]);
});

function quotePayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Sara Ali',
        'mobile' => '+20 111 564 6730',
        'email' => 'sara@example.com',
        'service_id' => null,
        'budget' => 'Less than $1,000',
        'start_timing' => 'As soon as possible',
        'page_url' => 'https://mindholding.net/en/services/x',
        'recaptcha_token' => 'token',
        'website' => '',
        'utm' => ['source' => 'google', 'medium' => 'cpc', 'campaign' => 'launch'],
    ], $overrides);
}

it('records a quote request, normalizes the mobile and returns a receipt', function () {
    $response = $this->postJson('/api/v1/public/leads/quote', quotePayload(['service_id' => $this->service->id]), [
        'Accept-Language' => 'en',
    ]);

    $response->assertCreated();
    expect($response->json('data.expected_response_time'))->toBe('We will respond within 24 working hours');
    expect($response->json('data.whatsapp_url'))->toStartWith('https://wa.me/');

    $lead = Lead::firstOrFail();
    expect($lead->type)->toBe('quote');
    expect($lead->status)->toBe('new');
    expect($lead->mobile)->toBe('+201115646730');
    expect($lead->language)->toBe('en');
    expect($lead->utm)->toBe(['source' => 'google', 'medium' => 'cpc', 'campaign' => 'launch']);
});

it('emails the sales address with the prospect WhatsApp link', function () {
    $this->postJson('/api/v1/public/leads/quote', quotePayload(['service_id' => $this->service->id]))->assertCreated();

    Mail::assertSent(NewLeadMail::class, function (NewLeadMail $mail) {
        return $mail->hasTo('info@mindholding.net')
            && str_contains($mail->body, 'https://wa.me/201115646730');
    });
});

it('requires a service for a quote request', function () {
    $response = $this->postJson('/api/v1/public/leads/quote', quotePayload());

    $response->assertStatus(422);
    expect($response->json('errors.service_id'))->not->toBeNull();
});

it('rejects a budget that is not one of the configured options', function () {
    $response = $this->postJson('/api/v1/public/leads/quote', quotePayload([
        'service_id' => $this->service->id,
        'budget' => 'Unlimited',
    ]));

    $response->assertStatus(422);
    expect($response->json('errors.budget'))->not->toBeNull();
});

it('rejects a mobile number outside Egypt and the Gulf', function () {
    $response = $this->postJson('/api/v1/public/leads/quote', quotePayload([
        'service_id' => $this->service->id,
        'mobile' => '+44 20 7946 0958',
    ]));

    $response->assertStatus(422);
    expect($response->json('errors.mobile'))->not->toBeNull();
});

it('records a demo request for a solution', function () {
    $response = $this->postJson('/api/v1/public/leads/demo', [
        'name' => 'Omar',
        'mobile' => '+971 50 336 5403',
        'business_name' => 'Cafe One',
        'solution_id' => $this->solution->id,
        'preferred_contact_time' => 'Evenings',
        'page_url' => 'https://mindholding.net/en/solutions/x',
        'recaptcha_token' => 'token',
    ]);

    $response->assertCreated();
    $lead = Lead::firstOrFail();
    expect($lead->type)->toBe('demo');
    expect($lead->solution_id)->toBe($this->solution->id);
});

it('routes a Gulf prospect to the Dubai WhatsApp number', function () {
    $response = $this->postJson('/api/v1/public/leads/callback', [
        'name' => 'Omar',
        'mobile' => '+966 50 123 4567',
        'page_url' => 'https://mindholding.net/ar',
        'recaptcha_token' => 'token',
    ]);

    $response->assertCreated();
    expect($response->json('data.whatsapp_url'))->toContain('971503365403');
});

it('routes an Egyptian prospect to the Egyptian WhatsApp number', function () {
    $response = $this->postJson('/api/v1/public/leads/callback', [
        'name' => 'Ahmed',
        'mobile' => '+20 111 564 6730',
        'page_url' => 'https://mindholding.net/ar',
        'recaptcha_token' => 'token',
    ]);

    $response->assertCreated();
    expect($response->json('data.whatsapp_url'))->toContain('201115646730');
});

it('requires name and mobile for a callback', function () {
    $response = $this->postJson('/api/v1/public/leads/callback', [
        'page_url' => 'https://mindholding.net/ar',
        'recaptcha_token' => 'token',
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.name'))->not->toBeNull();
    expect($response->json('errors.mobile'))->not->toBeNull();
});

it('stores a honeypot submission as spam, answers success and sends no email', function () {
    $response = $this->postJson('/api/v1/public/leads/callback', [
        'name' => 'Bot',
        'mobile' => '+20 111 564 6730',
        'page_url' => 'https://mindholding.net/ar',
        'recaptcha_token' => 'token',
        'website' => 'http://spam.example',
    ]);

    $response->assertCreated();
    expect(Lead::firstOrFail()->status)->toBe('spam');
    Mail::assertNothingSent();
});

it('rejects a submission that fails the reCAPTCHA check and stores nothing', function () {
    $this->app->instance(RecaptchaVerifier::class, new FakeRecaptchaVerifier(false));

    $response = $this->postJson('/api/v1/public/leads/callback', [
        'name' => 'Ahmed',
        'mobile' => '+20 111 564 6730',
        'page_url' => 'https://mindholding.net/ar',
        'recaptcha_token' => 'token',
    ]);

    $response->assertStatus(422);
    expect($response->json('errors.recaptcha_token'))->not->toBeNull();
    expect(Lead::count())->toBe(0);
    Mail::assertNothingSent();
});

it('limits lead submissions to five per hour per device', function () {
    $payload = [
        'name' => 'Ahmed',
        'mobile' => '+20 111 564 6730',
        'page_url' => 'https://mindholding.net/ar',
        'recaptcha_token' => 'token',
    ];

    for ($i = 0; $i < 5; $i++) {
        $this->withHeader('X-Device-Id', 'device-a')->postJson('/api/v1/public/leads/callback', $payload)->assertCreated();
    }

    $this->withHeader('X-Device-Id', 'device-a')->postJson('/api/v1/public/leads/callback', $payload)->assertStatus(429);
    expect(Lead::count())->toBe(5);
});
