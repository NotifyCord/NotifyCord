<?php

namespace NotifyCord\NotifyCord\Exceptions;

use Exception;
use GuzzleHttp\Exception\GuzzleException;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when there is no webhook URL or channel ID provided.
     *
     * @return static
     */
    public static function missingRecipient()
    {
        return new static('No Discord webhook URL or channel ID was provided.');
    }

    /**
     * Thrown when the notification does not return a Discord message instance.
     *
     * @param mixed $message
     * @return static
     */
    public static function invalidMessageObject($message)
    {
        $className = is_object($message) ? get_class($message) : gettype($message);

        return new static(
            "Discord notification must return a `NotifyCord\NotifyCord\DiscordMessage` instance. Received: {$className}"
        );
    }

    /**
     * Thrown when Discord responds with an error.
     *
     * @param \GuzzleHttp\Exception\GuzzleException $exception
     * @return static
     */
    public static function serviceRespondedWithError(GuzzleException $exception)
    {
        return new static(
            "Discord responded with an error: {$exception->getMessage()}"
        );
    }

    /**
     * Thrown when Discord returns a 429 Too Many Requests response.
     *
     * @param int $retryAfter
     * @return static
     */
    public static function rateLimited($retryAfter)
    {
        return new static(
            "Discord rate limit hit. Retry after {$retryAfter} seconds."
        );
    }

    /**
     * Thrown when no bot token is provided for channel-based messages.
     *
     * @return static
     */
    public static function missingBotToken()
    {
        return new static(
            'No Discord bot token was provided for channel-based messaging. Check your configuration.'
        );
    }

    /**
     * Thrown when a message is too large for Discord.
     *
     * @param int $size
     * @return static
     */
    public static function messageTooLarge($size)
    {
        return new static(
            "Discord message exceeds the maximum allowed size. Current size: {$size} bytes."
        );
    }
}
