<?php

namespace NotifyCord\NotifyCord;

use GuzzleHttp\Client;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class NotifyCordManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The default webhook URL.
     *
     * @var string|null
     */
    protected $defaultWebhook;

    /**
     * The bot token for channel-based messages.
     *
     * @var string|null
     */
    protected $botToken;

    /**
     * Whether to retry on rate limits.
     *
     * @var bool
     */
    protected $retryOnRateLimit;

    /**
     * Whether to retry on failures.
     *
     * @var bool
     */
    protected $retryOnFailure;

    /**
     * The maximum number of retries.
     *
     * @var int
     */
    protected $maxRetries;

    /**
     * The delay between retries in seconds.
     *
     * @var int
     */
    protected $retryDelay;

    /**
     * Whether to log errors.
     *
     * @var bool
     */
    protected $logErrors;

    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Create a new NotifyCord manager instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->http = new Client([
            'timeout' => $this->config('timeout', 5),
            'connect_timeout' => $this->config('connect_timeout', 5),
        ]);

        $this->defaultWebhook = $this->config('default_webhook');
        $this->botToken = $this->config('bot_token');
        $this->retryOnRateLimit = $this->config('retry_on_rate_limit', true);
        $this->retryOnFailure = $this->config('retry_on_failure', true);
        $this->maxRetries = $this->config('max_retries', 3);
        $this->retryDelay = $this->config('retry_delay', 2);
        $this->logErrors = $this->config('log_errors', true);
    }

    /**
     * Create a new Discord message instance.
     *
     * @param string $content
     * @return \NotifyCord\NotifyCord\DiscordMessage
     */
    public function message($content = '')
    {
        return new DiscordMessage($content);
    }

    /**
     * Get the Discord notification channel.
     *
     * @return \NotifyCord\NotifyCord\DiscordChannel
     */
    public function channel()
    {
        return new DiscordChannel($this->http, $this);
    }

    /**
     * Get a config value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function config($key, $default = null)
    {
        return $this->app['config']["notifycord.{$key}"] ?? $default;
    }

    /**
     * Determine if a default webhook is configured.
     *
     * @return bool
     */
    public function hasDefaultWebhook()
    {
        return ! empty($this->defaultWebhook);
    }

    /**
     * Get the default webhook URL.
     *
     * @return string
     */
    public function getDefaultWebhook()
    {
        return $this->defaultWebhook;
    }

    /**
     * Get the bot token.
     *
     * @return string|null
     */
    public function getBotToken()
    {
        return $this->botToken;
    }

    /**
     * Determine if rate limit retries are enabled.
     *
     * @return bool
     */
    public function shouldRetryOnRateLimit()
    {
        return $this->retryOnRateLimit;
    }

    /**
     * Determine if failure retries are enabled.
     *
     * @return bool
     */
    public function shouldRetryOnFailure()
    {
        return $this->retryOnFailure;
    }

    /**
     * Get the maximum number of retries.
     *
     * @return int
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * Get the delay between retries.
     *
     * @return int
     */
    public function getRetryDelay()
    {
        return $this->retryDelay;
    }

    /**
     * Determine if errors should be logged.
     *
     * @return bool
     */
    public function shouldLogErrors()
    {
        return $this->logErrors;
    }
}
