<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    /**
     * Test API connectivity
     */
    public function ping(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'API is working',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    }

    /**
     * Test authenticated endpoint
     */
    public function authenticatedPing(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Authentication is working',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'access_level' => $user->access_level
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get system info
     */
    public function systemInfo(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'app_name' => config('app.name'),
                'app_version' => '1.0.0',
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'database' => config('database.default'),
                'environment' => config('app.env'),
                'timezone' => config('app.timezone'),
                'api_version' => 'v1'
            ]
        ]);
    }
}
