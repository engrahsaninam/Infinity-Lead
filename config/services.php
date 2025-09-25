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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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
    'google_register' => [
        'client_id' => env('GOOGLE_REGISTER_CLIENT_ID'),
        'client_secret' => env('GOOGLE_REGISTER_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REGISTER_REDIRECT_URI'),
    ],
    'google_login' => [
        'client_id' => env('GOOGLE_LOGIN_CLIENT_ID'),
        'client_secret' => env('GOOGLE_LOGIN_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_LOGIN_REDIRECT_URI'),
    ],
    'google_gmail' => [
        'client_id' => env('GOOGLE_GMAIL_CLIENT_ID'),
        'client_secret' => env('GOOGLE_GMAIL_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_GMAIL_REDIRECT_URI'),
    ],
    'google_sheet' => [
        'client_id' => env('GOOGLE_SHEET_CLIENT_ID'),
        'client_secret' => env('GOOGLE_SHEET_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_SHEET_REDIRECT_URI'),
    ],

];
