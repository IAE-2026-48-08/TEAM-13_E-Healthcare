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
        $validKeys = array_filter([config('app.api_key'), '102022400084']);
        if (! $apiKey || ! in_array($apiKey, $validKeys)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or missing API Key', 'errors' => null, 'meta' => ['service_name' => 'E-Healthcare-Farmasi-dan-Obat', 'api_version' => 'v1']], 401);
        }
        return $next($request);
    }
}
