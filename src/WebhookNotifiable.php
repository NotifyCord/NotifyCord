<?php

namespace NotifyCord\NotifyCord;

use Illuminate\Notifications\Notifiable;

class WebhookNotifiable
{
    use Notifiable;

    protected $webhookUrl;

    public function __construct($webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function routeNotificationForDiscord()
    {
        return $this->webhookUrl;
    }
}