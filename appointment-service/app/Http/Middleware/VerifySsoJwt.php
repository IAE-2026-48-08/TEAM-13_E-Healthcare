<?php

namespace App\Http\Middleware;

use App\Models\LocalRole;
use Closure;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class VerifySsoJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (
            ! $authHeader ||
            ! preg_match('/^Bearer\s+(.+)$/i', trim($authHeader), $matches)
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Missing Bearer token.',
                'errors' => null,
            ], 401);
        }

        $token = trim($matches[1]);

        /*
         * Mengambil public key RS256 dari Central SSO.
         */
        try {
            $jwksResponse = Http::acceptJson()
                ->timeout(10)
                ->retry(2, 250)
                ->get(
                    env(
                        'IAE_SSO_JWKS_URL',
                        'https://iae-sso.virtualfri.id/api/v1/auth/jwks'
                    )
                );

            if (! $jwksResponse->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Central SSO JWKS is unavailable.',
                    'errors' => null,
                ], 503);
            }

            $jwks = $jwksResponse->json();

            if (
                ! is_array($jwks) ||
                ! isset($jwks['keys']) ||
                empty($jwks['keys'])
            ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid JWKS response from Central SSO.',
                    'errors' => null,
                ], 503);
            }
        } catch (Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to connect to Central SSO.',
                'errors' => null,
            ], 503);
        }

        /*
         * Memverifikasi:
         * - Signature RS256
         * - Key ID atau kid
         * - Expiration atau exp
         * - Not before atau nbf
         * - Issued at atau iat
         */
        try {
            JWT::$leeway = 60;

            $decoded = JWT::decode(
                $token,
                JWK::parseKeySet($jwks)
            );

            $payload = json_decode(
                json_encode($decoded),
                true
            );
        } catch (Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or expired JWT.',
                'errors' => null,
            ], 401);
        }

        /*
         * Mengambil identitas pengguna dari token SSO.
         */
        $identity = $payload['email']
            ?? $payload['sub']
            ?? $payload['nim']
            ?? null;

        if (! $identity) {
            return response()->json([
                'status' => 'error',
                'message' => 'JWT does not contain user identity.',
                'errors' => null,
            ], 401);
        }

        /*
         * Mengambil role dari claim role atau roles.
         */
        $roleClaim = $payload['role']
            ?? $payload['roles']
            ?? 'warga';

        if (is_array($roleClaim)) {
            $ssoRole = $roleClaim[0] ?? 'warga';
        } else {
            $ssoRole = (string) $roleClaim;
        }

        $normalizedRole = strtolower($ssoRole);

        $localRole = match ($normalizedRole) {
            'admin' => 'admin',
            'doctor', 'dokter' => 'doctor',
            default => 'patient',
        };

        /*
         * Menyimpan pemetaan role SSO ke role lokal.
         */
        LocalRole::updateOrCreate(
            ['sso_email' => $identity],
            [
                'sso_role' => $ssoRole,
                'local_role' => $localRole,
            ]
        );

        /*
         * Menyimpan data pengguna terverifikasi ke request.
         */
        $request->attributes->set('sso_user', [
            'identity' => $identity,
            'email' => $payload['email'] ?? null,
            'nim' => $payload['nim'] ?? null,
            'sso_role' => $ssoRole,
            'local_role' => $localRole,
            'payload' => $payload,
        ]);

        return $next($request);
    }
}
