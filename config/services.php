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

    'recaptcha' => [
        'secret' => env('RECAPTCHA_SECRET_KEY'),
        'url' => 'https://www.google.com/recaptcha/api/siteverify',
        'score_threshold' => (float) env('RECAPTCHA_SCORE_THRESHOLD', 0.5),
    ],

    /*
    | The Next.js frontend's on-demand revalidation endpoint. When set, a
    | redirect create/update/delete POSTs there so the frontend can refresh
    | its own redirect rules without a redeploy. Left unset, nothing is
    | called - this is a convenience webhook, not a required integration.
    */
    'frontend' => [
        'revalidate_url' => env('FRONTEND_REVALIDATE_URL'),
        'revalidate_secret' => env('FRONTEND_REVALIDATE_SECRET'),
    ],

];
