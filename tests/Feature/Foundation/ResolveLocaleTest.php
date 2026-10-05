<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::prefix('api/v1')->middleware(['api'])->group(function () {
        Route::get('public/_probe-locale', fn () => response()->json(['locale' => app()->getLocale()]));
    });
});

it('defaults to arabic when no accept-language header is sent', function () {
    // Symfony's test Request::create() always injects a default
    // "en-us,en;q=0.5" Accept-Language when none is given, mimicking a real
    // browser. To exercise the true "header absent" branch we have to blank
    // out the server variable directly.
    $response = $this->withServerVariables(['HTTP_ACCEPT_LANGUAGE' => ''])->getJson('/api/v1/public/_probe-locale');

    $response->assertJson(['locale' => 'ar']);
});

it('resolves english from the accept-language header', function () {
    $response = $this->getJson('/api/v1/public/_probe-locale', ['Accept-Language' => 'en']);

    $response->assertJson(['locale' => 'en']);
});

it('falls back to arabic for an unsupported locale', function () {
    $response = $this->getJson('/api/v1/public/_probe-locale', ['Accept-Language' => 'fr-FR,fr;q=0.9']);

    $response->assertJson(['locale' => 'ar']);
});

it('picks the first supported locale from a weighted list', function () {
    $response = $this->getJson('/api/v1/public/_probe-locale', ['Accept-Language' => 'fr-FR;q=0.9,en;q=0.8']);

    $response->assertJson(['locale' => 'en']);
});
