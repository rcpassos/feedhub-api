<?php

use App\Services\HealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

$headers = ['x-api-key' => env('API_KEY', 'test-api-key')];

test('health endpoint returns correct structure', function () use ($headers) {
    // Mock the HealthService
    $healthData = [
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'application' => [
            'name' => config('app.name'),
            'environment' => 'testing',
        ],
        'system' => [
            'php_version' => PHP_VERSION,
        ],
        'dependencies' => [
            'database' => ['connected' => true],
            'cache' => ['working' => true],
        ],
    ];

    $this->mock(HealthService::class, function ($mock) use ($healthData) {
        $mock->shouldReceive('getHealthData')
            ->once()
            ->andReturn($healthData);
    });

    $response = $this->withHeaders($headers)->getJson('/api/health');

    $response->assertStatus(200)
        ->assertJson([
            'data' => $healthData,
        ]);
});

test('health endpoint handles service errors gracefully', function () use ($headers) {
    // Mock the HealthService with error state
    $errorData = [
        'status' => 'error',
        'timestamp' => now()->toIso8601String(),
        'application' => [
            'name' => config('app.name'),
            'environment' => 'testing',
        ],
        'dependencies' => [
            'database' => [
                'connected' => false,
                'error' => 'Could not connect to database',
            ],
        ],
    ];

    $this->mock(HealthService::class, function ($mock) use ($errorData) {
        $mock->shouldReceive('getHealthData')
            ->once()
            ->andReturn($errorData);
    });

    $response = $this->withHeaders($headers)->getJson('/api/health');

    $response->assertStatus(200)
        ->assertJson([
            'data' => $errorData,
        ]);
});

test('health service is properly injected into controller', function () use ($headers) {
    // This test verifies that Laravel's dependency injection is working correctly
    // We're checking that our controller can resolve the HealthService from the container

    $this->mock(HealthService::class, function ($mock) {
        $mock->shouldReceive('getHealthData')
            ->once()
            ->andReturn(['status' => 'ok']);
    });

    $response = $this->withHeaders($headers)->getJson('/api/health');

    $response->assertStatus(200);
    // The assertion that matters is that the mock was called, which would fail
    // if the service wasn't properly injected
});
