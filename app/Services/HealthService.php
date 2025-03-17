<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthService
{
    /**
     * Get comprehensive health data about the application
     */
    public function getHealthData(): array
    {
        // Track execution time
        $startTime = microtime(true);

        // Collect health data
        $health = [
            'status' => 'ok',
            'timestamp' => Carbon::now()->toIso8601String(),
            'application' => [
                'name' => config('app.name'),
                'environment' => config('app.env'),
                'debug' => config('app.debug'),
                'version' => config('app.version', '1.0.0'),
                'url' => config('app.url'),
            ],
            'system' => [
                'php_version' => phpversion(),
                'memory_usage' => $this->getMemoryUsage(),
                'server_time' => date('Y-m-d H:i:s'),
            ],
            'dependencies' => [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
            ],
        ];

        // Calculate response time
        $health['performance'] = [
            'response_time' => round(microtime(true) - $startTime, 4).'s',
        ];

        return $health;
    }

    /**
     * Get formatted memory usage
     */
    private function getMemoryUsage(): string
    {
        $memory = memory_get_usage(true);
        $unit = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($memory, 1024));

        return round($memory / 1024 ** $i, 2).' '.$unit[$i];
    }

    /**
     * Check database connection and get some stats
     */
    private function checkDatabase(): array
    {
        try {
            // Test basic connection
            $connection = DB::connection();
            $connected = true;

            // Get some stats
            $stats = [
                'connection' => $connection->getName(),
                'driver' => $connection->getDriverName(),
            ];

            // Try a simple query (count users)
            $userCount = User::count();
            $querySuccess = true;
            $stats['user_count'] = $userCount;

        } catch (\Exception $e) {
            $connected = false;
            $querySuccess = false;
            $stats = [
                'error' => $e->getMessage(),
            ];
        }

        return [
            'connected' => $connected,
            'query_successful' => $querySuccess,
            'stats' => $stats,
        ];
    }

    /**
     * Check cache connection
     */
    private function checkCache(): array
    {
        try {
            $key = 'health-check-'.time();
            $value = time();

            // Test storing in cache
            Cache::put($key, $value, 1);

            // Test retrieving from cache
            $retrieved = Cache::get($key);
            $success = $retrieved === $value;

            // Clean up
            Cache::forget($key);

            $stats = [
                'driver' => config('cache.default'),
            ];

        } catch (\Exception $e) {
            $success = false;
            $stats = [
                'error' => $e->getMessage(),
            ];
        }

        return [
            'working' => $success,
            'stats' => $stats,
        ];
    }
}
