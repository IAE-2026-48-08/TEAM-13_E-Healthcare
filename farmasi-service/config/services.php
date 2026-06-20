<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

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
	'internal' => [
        'data_pasien_url' => env('DATA_PASIEN_SERVICE_URL', 'http://data-pasien-service:8000'),
        'data_pasien_key' => env('DATA_PASIEN_API_KEY', '102022400238'),
        'appointment_url' => env('APPOINTMENT_SERVICE_URL', 'http://appointment-service:8000'),
        'appointment_key' => env('APPOINTMENT_API_KEY', 'KEY-MHS-157'),
    ],

];
