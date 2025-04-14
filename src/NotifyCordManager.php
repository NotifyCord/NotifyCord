<?php

namespace NotifyCord\NotifyCord;

use GuzzleHttp\Client;
use Illuminate\Support\Manager;
use Illuminate\Support\Facades\Log;
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
    
    /**
     * Send a simple message to a Discord webhook URL.
     *
     * @param string $message The message text
     * @param string|null $webhookUrl Optional webhook URL (uses default if not provided)
     * @param callable|null $callback Optional callback for customizing the message
     * @return bool Success or failure
     */
    public function sendMessage($message, $webhookUrl = null, $callback = null)
    {
        try {
            $webhookUrl = $webhookUrl ?: $this->getDefaultWebhook();
            
            $notifiable = new WebhookNotifiable($webhookUrl);
            $notification = new SimpleDiscordNotification($message, $callback);
            
            $notifiable->notify($notification);
            
            return true;
            
        } catch (\Exception $e) {
            if ($this->shouldLogErrors()) {
                $logChannel = $this->config('log_channel', 'stack');
                $logFile = $this->config('log_file');
                
                if ($logFile) {
                    Log::channel($logChannel)->error('Discord notification failed: ' . $e->getMessage(), [
                        'exception' => $e,
                        'webhook' => $this->maskWebhookUrl($webhookUrl),
                    ]);
                    
                    // Özel log dosyasına da yazma
                    file_put_contents(
                        $logFile, 
                        '[' . date('Y-m-d H:i:s') . '] Discord notification failed: ' . $e->getMessage() . 
                        ' Webhook: ' . $this->maskWebhookUrl($webhookUrl) . "\n", 
                        FILE_APPEND
                    );
                } else {
                    Log::channel($logChannel)->error('Discord notification failed: ' . $e->getMessage(), [
                        'exception' => $e,
                        'webhook' => $this->maskWebhookUrl($webhookUrl),
                    ]);
                }
            }
            
            return false;
        }
    }
    
    /**
     * Mask the webhook URL for security when logging.
     *
     * @param string $webhookUrl
     * @return string
     */
    protected function maskWebhookUrl($webhookUrl)
    {
        if (!$webhookUrl) return 'null';
        
        $parts = explode('/', $webhookUrl);
        $lastPart = end($parts);
        
        if (strlen($lastPart) > 8) {
            return str_replace($lastPart, substr($lastPart, 0, 4) . '...' . substr($lastPart, -4), $webhookUrl);
        }
        
        return 'https://discord.com/api/webhooks/***';
    }
}
