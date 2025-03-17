<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

$headers = ['x-api-key' => 'test-api-key'];

test('registers a new user and returns auth token', function () use ($headers) {
    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->withHeaders($headers)
        ->postJson('/api/auth/register', $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token' => [
                    'token',
                    'expires_at',
                ],
            ],
        ]);
});

test('login with valid credentials returns auth token', function () use ($headers) {
    $password = 'password';
    $user = User::factory()->create([
        'password' => bcrypt($password),
    ]);

    $payload = [
        'email' => $user->email,
        'password' => $password,
    ];

    $response = $this->withHeaders($headers)
        ->postJson('/api/auth/login', $payload);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token' => [
                    'token',
                    'expires_at',
                ],
            ],
        ]);
});

test('authenticated user can access me endpoint', function () use ($headers) {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->withHeaders($headers)
        ->getJson('/api/auth/me');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
});

test('authenticated user can logout successfully', function () use ($headers) {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->withHeaders($headers)
        ->postJson('/api/auth/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Logged out successfully',
        ]);
});

test('authenticated user can refresh token', function () use ($headers) {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->withHeaders($headers)
        ->postJson('/api/auth/refresh');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token' => [
                    'token',
                    'expires_at',
                ],
            ],
        ]);
});
