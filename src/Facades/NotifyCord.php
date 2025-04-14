<?php

namespace NotifyCord\NotifyCord\Facades;

use Illuminate\Support\Facades\Facade;
use NotifyCord\NotifyCord\DiscordMessage;

/**
 * @method static \NotifyCord\NotifyCord\DiscordMessage message(string $content = '')
 * @method static bool hasDefaultWebhook()
 * @method static string getDefaultWebhook()
 * @method static string getBotToken()
 * @method static bool shouldRetryOnRateLimit()
 * @method static bool shouldRetryOnFailure()
 * @method static int getMaxRetries()
 * @method static int getRetryDelay()
 * @method static bool shouldLogErrors()
 * 
 * @see \NotifyCord\NotifyCord\NotifyCordManager
 */
class NotifyCord extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'notifycord';
    }
}
