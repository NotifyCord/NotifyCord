<?php

namespace NotifyCord\NotifyCord;

use Illuminate\Notifications\Notification;

class SimpleDiscordNotification extends Notification
{
    /**
     * The message to send.
     *
     * @var string
     */
    protected $message;

    /**
     * Optional callback for customizing the message.
     *
     * @var callable|null
     */
    protected $callback;

    /**
     * Create a new notification instance.
     *
     * @param string $message The message text
     * @param callable|null $callback Optional callback for customizing the message
     * @return void
     */
    public function __construct($message, $callback = null)
    {
        $this->message = $message;
        $this->callback = $callback;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['discord'];
    }

    /**
     * Get the Discord representation of the notification.
     *
     * @param mixed $notifiable
     * @return \NotifyCord\NotifyCord\DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        $discordMessage = app(NotifyCordManager::class)->message($this->message);
        
        if (is_callable($this->callback)) {
            call_user_func($this->callback, $discordMessage);
        }
        
        return $discordMessage;
    }
}