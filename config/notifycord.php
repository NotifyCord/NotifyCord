<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Discord Mode Configuration
    |--------------------------------------------------------------------------
    |
    | Choose between 'webhook' or 'bot' mode
    |
    */
    'mode' => env('DISCORD_MODE', 'webhook'),

    /*
    |--------------------------------------------------------------------------
    | Discord Credentials
    |--------------------------------------------------------------------------
    |
    | Credentials based on selected mode
    |
    */
    'default_webhook' => env('DISCORD_WEBHOOK_URL'),
    'bot_token' => env('DISCORD_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Error Handling Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how errors are handled and displayed
    |
    */
    'display_errors' => env('DISCORD_DISPLAY_ERRORS', true),
    'error_detail_level' => env('DISCORD_ERROR_DETAIL_LEVEL', 'detailed'), // basic, detailed, debug
    
    /*
    |--------------------------------------------------------------------------
    | Default Channel ID
    |--------------------------------------------------------------------------
    |
    | Default channel ID for all notifications if specific channels are not set
    |
    */
    'default_channel_id' => env('NOTIFYCORD_CHANNEL_ID'),

    /*
    |--------------------------------------------------------------------------
    | Channel IDs
    |--------------------------------------------------------------------------
    |
    | Specific channel IDs for different types of notifications
    |
    */
    'channels' => [
        'exceptions' => env('DISCORD_EXCEPTION_CHANNEL_ID'),
        'deployments' => env('DISCORD_DEPLOYMENT_CHANNEL_ID'),
        'logs' => env('DISCORD_LOG_CHANNEL_ID'),
        'alerts' => env('DISCORD_ALERT_CHANNEL_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Discord Channels
    |--------------------------------------------------------------------------
    |
    | Channel IDs for different notification types
    |
    */
    'channels' => [
        'exceptions' => env('DISCORD_EXCEPTION_CHANNEL_ID'),
        'deployments' => env('DISCORD_DEPLOYMENT_CHANNEL_ID'),
        'logs' => env('DISCORD_LOG_CHANNEL_ID'),
        'alerts' => env('DISCORD_ALERT_CHANNEL_ID'),
    ],

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