<?php

namespace NotifyCord\NotifyCord;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotifyCord\NotifyCord\Exceptions\CouldNotSendNotification;

class DiscordChannel
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * The NotifyCord manager instance.
     *
     * @var \NotifyCord\NotifyCord\NotifyCordManager
     */
    protected $manager;

    /**
     * Create a new Discord channel instance.
     *
     * @param \GuzzleHttp\Client $http
     * @param \NotifyCord\NotifyCord\NotifyCordManager $manager
     * @return void
     */
    public function __construct(Client $http, NotifyCordManager $manager)
    {
        $this->http = $http;
        $this->manager = $manager;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     *
     * @throws \NotifyCord\NotifyCord\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $route = $notifiable->routeNotificationFor('discord', $notification)) {
            // If no Discord route is specified, check if we should use the default webhook
            if (! $this->manager->hasDefaultWebhook()) {
                throw CouldNotSendNotification::missingRecipient();
            }

            $route = $this->manager->getDefaultWebhook();
        }

        $message = $notification->toDiscord($notifiable);

        if (is_string($message)) {
            $message = DiscordMessage::create($message);
        }

        if (! $message instanceof DiscordMessage) {
            throw CouldNotSendNotification::invalidMessageObject($message);
        }

        // Determine if we're sending via webhook or channel
        if ($this->looksLikeWebhookUrl($route)) {
            return $this->sendToWebhook($route, $message);
        } else {
            // Assume it's a channel ID and use bot token
            return $this->sendToChannel($route, $message);
        }
    }

    /**
     * Determine if the given route is a Discord webhook URL.
     *
     * @param string $route
     * @return bool
     */
    protected function looksLikeWebhookUrl($route)
    {
        return strpos($route, 'discord.com/api/webhooks/') !== false || 
               preg_match('/^https:\/\/[^\/]+\/api\/webhooks\/\d+\/\S+$/', $route);
    }

    /**
     * Send the notification to a Discord webhook URL.
     *
     * @param string $webhook
     * @param DiscordMessage $message
     * @return void
     *
     * @throws \NotifyCord\NotifyCord\Exceptions\CouldNotSendNotification
     */
    protected function sendToWebhook($webhook, DiscordMessage $message)
    {
        $payload = $message->toArray();
        
        // Check message size to prevent 400 Bad Request errors
        $messageSize = strlen(json_encode($payload));
        if ($messageSize > 8000000) { // 8MB limit for Discord
            throw CouldNotSendNotification::messageTooLarge($messageSize);
        }

        try {
            $response = $this->http->post($webhook, [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'NotifyCord/1.0 (+https://github.com/NotifyCord/NotifyCord)',
                ],
            ]);

            // Check for rate limits
            if ($response->getStatusCode() === 429) {
                $retryAfter = $response->getHeader('Retry-After')[0] ?? 5;
                
                if ($this->manager->shouldRetryOnRateLimit()) {
                    sleep((int) $retryAfter);
                    return $this->sendToWebhook($webhook, $message);
                }
                
                throw CouldNotSendNotification::rateLimited($retryAfter);
            }

            return $response;
        } catch (GuzzleException $exception) {
            if ($this->manager->shouldLogErrors()) {
                Log::error('NotifyCord webhook notification failed: ' . $exception->getMessage());
            }
            
            if ($this->manager->shouldRetryOnFailure() && $message->getRetryCount() < $this->manager->getMaxRetries()) {
                $message->incrementRetryCount();
                sleep($this->manager->getRetryDelay());
                return $this->sendToWebhook($webhook, $message);
            }
            
            throw CouldNotSendNotification::serviceRespondedWithError($exception);
        }
    }

    /**
     * Send the notification to a Discord channel using bot token.
     *
     * @param string $channelId
     * @param DiscordMessage $message
     * @return void
     *
     * @throws \NotifyCord\NotifyCord\Exceptions\CouldNotSendNotification
     */
    protected function sendToChannel($channelId, DiscordMessage $message)
    {
        $token = $this->manager->getBotToken();
        
        if (empty($token)) {
            throw CouldNotSendNotification::missingBotToken();
        }
        
        $endpoint = "https://discord.com/api/v10/channels/{$channelId}/messages";
        $payload = $message->toArray();
        
        // Check message size to prevent 400 Bad Request errors
        $messageSize = strlen(json_encode($payload));
        if ($messageSize > 8000000) { // 8MB limit for Discord
            throw CouldNotSendNotification::messageTooLarge($messageSize);
        }
        
        try {
            $response = $this->http->post($endpoint, [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bot {$token}",
                    'User-Agent' => 'NotifyCord/1.0 (+https://github.com/NotifyCord/NotifyCord)',
                ],
            ]);

            // Check for rate limits
            if ($response->getStatusCode() === 429) {
                $retryAfter = $response->getHeader('Retry-After')[0] ?? 5;
                
                if ($this->manager->shouldRetryOnRateLimit()) {
                    sleep((int) $retryAfter);
                    return $this->sendToChannel($channelId, $message);
                }
                
                throw CouldNotSendNotification::rateLimited($retryAfter);
            }

            return $response;
        } catch (GuzzleException $exception) {
            if ($this->manager->shouldLogErrors()) {
                Log::error('NotifyCord channel notification failed: ' . $exception->getMessage());
            }
            
            if ($this->manager->shouldRetryOnFailure() && $message->getRetryCount() < $this->manager->getMaxRetries()) {
                $message->incrementRetryCount();
                sleep($this->manager->getRetryDelay());
                return $this->sendToChannel($channelId, $message);
            }
            
            throw CouldNotSendNotification::serviceRespondedWithError($exception);
        }
    }
}
