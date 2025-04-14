<?php

namespace NotifyCord\NotifyCord\Facades;

use Illuminate\Support\Facades\Facade;
use NotifyCord\NotifyCord\Helpers\DiscordShortcuts as DiscordShortcutsHelper;

/**
 * @method static bool success(string $title, string $message, array $fields = [], string $webhookUrl = null)
 * @method static bool info(string $title, string $message, array $fields = [], string $webhookUrl = null)
 * @method static bool warning(string $title, string $message, array $fields = [], string $webhookUrl = null)
 * @method static bool error(string $title, string $message, array $fields = [], string $webhookUrl = null)
 * @method static bool exception(\Throwable $exception, string $webhookUrl = null)
 * @method static bool serverStatus(array $metrics, string $webhookUrl = null)
 * @method static bool deployment(string $environment, string $version, string $deployer, array $additionalFields = [], string $webhookUrl = null)
 * @method static bool newUser(string $username, string $email, array $additionalFields = [], string $webhookUrl = null)
 * @method static bool order(string $orderNumber, string $amount, string $customer, array $additionalFields = [], string $webhookUrl = null)
 * 
 * @see \NotifyCord\NotifyCord\Helpers\DiscordShortcuts
 */
class DiscordShortcuts extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return DiscordShortcutsHelper::class;
    }

    /**
     * Resolve a new instance of the facade's target.
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = new DiscordShortcutsHelper();

        return $instance->$method(...$args);
    }
}