<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    use ApiResponse; // Memanggil trait standar respon

    public function handle(Request $request, Closure $next): Response
    {
        // API key Data Pasien Service dibaca dari environment Docker
        $expectedKey = env('IAE_API_KEY', 'KEY-MHS-279');
        $providedKey = $request->header('X-IAE-KEY');

        if (!$providedKey || $providedKey !== $expectedKey) {
            return $this->errorResponse('Unauthorized. Header X-IAE-KEY tidak valid atau tidak ditemukan.', 401);
        }

        return $next($request);
    }
}