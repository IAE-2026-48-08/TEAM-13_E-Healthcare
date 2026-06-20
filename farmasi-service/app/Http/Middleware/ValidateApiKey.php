<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-IAE-KEY');

        $validApiKey = env('IAE_API_KEY', 'KEY-MHS-157');

        if (! $apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or missing API Key',
                'errors' => null,
                'meta' => [
                    'service_name' => 'E-Healthcare-Farmasi-dan-Obat',
                    'api_version' => 'v1',
                ],
            ], 401);
        }

        return $next($request);
    }
}
