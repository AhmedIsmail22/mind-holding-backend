<?php

use App\Support\Leads\GoogleRecaptchaVerifier;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['services.recaptcha.secret' => 'test-secret', 'services.recaptcha.score_threshold' => 0.5]);
});

it('accepts a token with a passing score and the expected action', function () {
    Http::fake(['www.google.com/*' => Http::response(['success' => true, 'score' => 0.9, 'action' => 'quote'])]);

    expect((new GoogleRecaptchaVerifier)->verify('tok', 'quote', '1.2.3.4'))->toBeTrue();

    Http::assertSent(fn ($request) => $request['secret'] === 'test-secret' && $request['response'] === 'tok');
});

it('rejects a low score', function () {
    Http::fake(['www.google.com/*' => Http::response(['success' => true, 'score' => 0.2, 'action' => 'quote'])]);

    expect((new GoogleRecaptchaVerifier)->verify('tok', 'quote', null))->toBeFalse();
});

it('rejects a token issued for a different action', function () {
    Http::fake(['www.google.com/*' => Http::response(['success' => true, 'score' => 0.9, 'action' => 'callback'])]);

    expect((new GoogleRecaptchaVerifier)->verify('tok', 'quote', null))->toBeFalse();
});

it('rejects when Google says the token is invalid', function () {
    Http::fake(['www.google.com/*' => Http::response(['success' => false, 'error-codes' => ['invalid-input-response']])]);

    expect((new GoogleRecaptchaVerifier)->verify('tok', 'quote', null))->toBeFalse();
});

it('fails closed without calling Google when no secret is configured in production', function () {
    app()->detectEnvironment(fn () => 'production');
    config(['services.recaptcha.secret' => null]);
    Http::fake();

    expect((new GoogleRecaptchaVerifier)->verify('tok', 'quote', null))->toBeFalse();
    Http::assertNothingSent();
});

it('rejects the placeholder token in production even if a secret is configured', function () {
    app()->detectEnvironment(fn () => 'production');
    Http::fake();

    expect((new GoogleRecaptchaVerifier)->verify(GoogleRecaptchaVerifier::PLACEHOLDER_TOKEN, 'quote', null))->toBeFalse();
    Http::assertNothingSent();
});

it('bypasses the missing-secret check in local/testing so manual dev testing is not blocked', function () {
    config(['services.recaptcha.secret' => null]);
    Http::fake();

    expect((new GoogleRecaptchaVerifier)->verify('tok', 'quote', null))->toBeTrue();
    Http::assertNothingSent();
});

it('accepts the placeholder token in local/testing without calling Google', function () {
    Http::fake();

    expect((new GoogleRecaptchaVerifier)->verify(GoogleRecaptchaVerifier::PLACEHOLDER_TOKEN, 'quote', null))->toBeTrue();
    Http::assertNothingSent();
});
