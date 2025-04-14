<?php

namespace NotifyCord\NotifyCord;

use GuzzleHttp\Client;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class NotifyCordServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();

        // Register the Discord channel with Laravel
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('discord', function ($app) {
                return $app->make(DiscordChannel::class);
            });
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/notifycord.php', 'notifycord'
        );

        $this->app->singleton('notifycord', function ($app) {
            return new NotifyCordManager($app);
        });
        
        $this->app->singleton('discord.shortcuts', function ($app) {
            return new \NotifyCord\NotifyCord\Helpers\DiscordShortcuts();
        });

        $this->app->singleton(DiscordChannel::class, function ($app) {
            return new DiscordChannel(
                new Client(['timeout' => $app['config']->get('notifycord.timeout', 5)]),
                $app->make('notifycord')
            );
        });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/notifycord.php' => config_path('notifycord.php'),
            ], 'notifycord-config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'notifycord',
            'discord.shortcuts',
            DiscordChannel::class,
        ];
    }
}
