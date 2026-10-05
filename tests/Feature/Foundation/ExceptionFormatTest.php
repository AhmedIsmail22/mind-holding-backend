<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

it('returns a json 404 envelope for an unknown api route', function () {
    $response = $this->getJson('/api/v1/public/does-not-exist');

    $response->assertStatus(404);
    $response->assertJson([
        'success' => false,
        'message' => 'Resource not found.',
    ]);
});

it('returns a json 401 envelope for an unauthenticated admin route', function () {
    Route::prefix('api/v1')->middleware(['api', 'auth:sanctum'])->group(function () {
        Route::get('admin/_probe', fn () => response()->json(['ok' => true]));
    });

    $response = $this->getJson('/api/v1/admin/_probe');

    $response->assertStatus(401);
    $response->assertJson([
        'success' => false,
        'message' => 'Unauthenticated.',
    ]);
});

it('returns a json 403 envelope when the user lacks the required permission', function () {
    Permission::findOrCreate('_probe.manage');

    Route::prefix('api/v1')->middleware(['api', 'auth:sanctum', 'permission:_probe.manage'])->group(function () {
        Route::get('admin/_probe-permission', fn () => response()->json(['ok' => true]));
    });

    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/admin/_probe-permission');

    $response->assertStatus(403);
    expect($response->json('success'))->toBeFalse();
});

it('returns a json 422 envelope for a validation failure', function () {
    Route::prefix('api/v1')->middleware(['api'])->group(function () {
        Route::post('public/_probe-validate', function (Request $request) {
            $request->validate(['name' => 'required|string']);

            return response()->json(['ok' => true]);
        });
    });

    $response = $this->postJson('/api/v1/public/_probe-validate', []);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'The given data was invalid.',
    ]);
    expect($response->json('errors.name'))->not->toBeNull();
});
