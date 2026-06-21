<?php

return [

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'data_pasien' => [
        'url' => env(
            'DATA_PASIEN_SERVICE_URL',
            'http://data-pasien-service:8000'
        ),
        'api_key' => env(
            'DATA_PASIEN_API_KEY',
            'KEY-MHS-279'
        ),
    ],

    'farmasi' => [
        'url' => env(
            'FARMASI_SERVICE_URL',
            'http://farmasi-service:8000'
        ),
        'api_key' => env(
            'FARMASI_API_KEY',
            'KEY-MHS-157'
        ),
    ],

    'iae_sso' => [
        'token_url' => env(
            'IAE_SSO_TOKEN_URL',
            'https://iae-sso.virtualfri.id/api/v1/auth/token'
        ),
        'api_key' => env(
            'IAE_API_KEY',
            'KEY-MHS-390'
        ),
        'nim' => env(
            'IAE_NIM',
            '102022400300'
        ),
    ],

];