<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HealthService;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __construct(protected HealthService $healthService) {}

    public function __invoke(): JsonResponse
    {
        $healthData = $this->healthService->getHealthData();

        return response()->json([
            'data' => $healthData,
        ]);
    }
}
