<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Discord Webhook URL
    |--------------------------------------------------------------------------
    |
    | This is the default webhook URL that will be used when no specific
    | webhook URL is provided for a notification.
    |
    */
    'default_webhook' => env('DISCORD_WEBHOOK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Discord Bot Token (Not used in webhook-only mode)
    |--------------------------------------------------------------------------
    |
    | This token is required if you want to send messages to specific Discord
    | channels directly (instead of using webhooks). You'll need to create a
    | bot on Discord's developer portal to get this token.
    | Note: This package currently focuses on webhook-based notifications only.
    |
    */
    // 'bot_token' => env('DISCORD_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control retry behavior when sending notifications fails
    | or when rate limits are hit.
    |
    */
    'retry_on_rate_limit' => true,
    'retry_on_failure' => true,
    'max_retries' => 3,
    'retry_delay' => 2, // seconds

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Options
    |--------------------------------------------------------------------------
    |
    | Configure the HTTP client used to send messages to Discord.
    |
    */
    'timeout' => 5, // seconds
    'connect_timeout' => 5, // seconds

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configure how errors are logged. You can specify a custom log file path
    | and the Laravel log channel to use. When messages aren't sent but no
    | errors appear, check the notifycord.log file for troubleshooting.
    |
    */
    'log_errors' => true,
    'log_file' => storage_path('logs/notifycord.log'),
    'log_channel' => 'stack',
    'debug' => env('NOTIFYCORD_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | By default, notifications are queued using Laravel's queue system.
    | These settings provide additional control over how Discord notifications
    | are queued.
    |
    */
    'queue' => [
        'enabled' => true,
        'connection' => env('QUEUE_CONNECTION', 'sync'),
        'queue' => 'default',
    ],
];
