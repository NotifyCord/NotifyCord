# NotifyCord

A powerful and flexible package for sending Discord notifications from your Laravel application. NotifyCord provides a clean and simple way to send rich Discord messages through channels, webhooks, and integrates perfectly with Laravel's notification system.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/notifycord/notifycord.svg?style=flat-square)](https://packagist.org/packages/notifycord/notifycord)
[![Total Downloads](https://img.shields.io/packagist/dt/notifycord/notifycord.svg?style=flat-square)](https://packagist.org/packages/notifycord/notifycord)
[![GitHub Repository](https://img.shields.io/badge/github-NotifyCord%2FNotifyCord-blue?style=flat-square)](https://github.com/NotifyCord/NotifyCord)

## Features

- 🔌 Seamless integration with Laravel's Notification system
- 🤖 Support for bot-based messaging (to channels and users)
- 🔗 Support for webhook-based notifications
- 📋 Rich embed message support (title, description, fields, colors, etc.)
- 🔘 Discord components support (buttons, action rows)
- 🔁 Automatic rate limit handling and retries
- 🧩 Queue-compatible for asynchronous notifications
- 🛡️ Error handling with detailed exceptions
- ⚙️ Comprehensive configuration options

## Requirements

- PHP 8.0 or higher
- Laravel 9.0 or higher
- GuzzleHTTP 7.0 or higher

## Installation

You can install the package via composer:

```bash
composer require notifycord/notifycord
```

The package will automatically register its service provider if you're using Laravel's package auto-discovery.

If you're using Laravel without auto-discovery, add the service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    NotifyCord\NotifyCord\NotifyCordServiceProvider::class,
],

'aliases' => [
    // ...
    'NotifyCord' => NotifyCord\NotifyCord\Facades\NotifyCord::class,
],
```

### Publishing the configuration

You can publish the configuration file with:

```bash
php artisan vendor:publish --tag="notifycord-config"
```

This will create a `config/notifycord.php` configuration file in your application with the following options:

```php
return [
    'default_webhook' => env('DISCORD_WEBHOOK_URL'),
    'bot_token' => env('DISCORD_BOT_TOKEN'),
    'retry_on_rate_limit' => true,
    'retry_on_failure' => true,
    'max_retries' => 3,
    'retry_delay' => 2, // seconds
    'timeout' => 5, // seconds
    'connect_timeout' => 5, // seconds
    'log_errors' => true,
    // ...
];
```

### Environment Configuration

Add the following to your `.env` file:

```
DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/your-webhook-id/your-webhook-token
DISCORD_BOT_TOKEN=your-bot-token  # Optional, only if you need to send to specific channels
```

## Usage

### Laravel Notifications

The easiest way to use NotifyCord is through Laravel's notification system. First, create a notification:

```bash
php artisan make:notification DiscordNotification
```

Then, modify the generated notification class to include a `toDiscord` method:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotifyCord\NotifyCord\Facades\NotifyCord;

class DiscordNotification extends Notification
{
    protected $message;
    
    public function __construct($message)
    {
        $this->message = $message;
    }
    
    public function via($notifiable)
    {
        return ['discord'];
    }
    
    public function toDiscord($notifiable)
    {
        return NotifyCord::message($this->message)
            ->embed(function ($embed) {
                $embed->title('Important Notification')
                     ->description('This is an important notification from your application')
                     ->color('#ff0000')
                     ->timestamp()
                     ->footer('Your Application', 'https://example.com/logo.png')
                     ->field('Status', 'Active', true)
                     ->field('Environment', app()->environment(), true);
            })
            ->button('View Details', 'primary', 'view_details')
            ->button('Visit Dashboard', 'link', null, 'https://dashboard.example.com');
    }
}
```

To make your model "notifiable" via Discord, add the `routeNotificationForDiscord` method to it:

```php
<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    public function routeNotificationForDiscord()
    {
        // Return a webhook URL or channel ID
        return 'https://discord.com/api/webhooks/your-webhook-id/your-webhook-token';
        
        // Or return a channel ID if using bot token
        // return '123456789012345678';
    }
}
```

Then send the notification:

```php
$user->notify(new DiscordNotification('Hello from NotifyCord!'));
```

### Direct Usage

You can also use NotifyCord directly without a notification class:

```php
use NotifyCord\NotifyCord\Facades\NotifyCord;

// Send to the default webhook configured in .env
NotifyCord::channel()->send(null, new class {
    public function toDiscord() {
        return NotifyCord::message('Direct message using NotifyCord!')
            ->embed(function ($embed) {
                $embed->title('Direct Usage Example')
                     ->description('This message was sent directly without a notification class')
                     ->color('#00ff00');
            });
    }
});

// Or specify a custom webhook URL
$webhookUrl = 'https://discord.com/api/webhooks/custom/webhook';
$notifiable = new class {
    public function routeNotificationForDiscord() {
        return $webhookUrl;
    }
};

NotifyCord::channel()->send($notifiable, new class {
    public function toDiscord() {
        return NotifyCord::message('Custom webhook message');
    }
});
```

## Message Components

### Embeds

Discord embeds provide rich formatting options:

```php
NotifyCord::message('Message with embed')
    ->embed(function ($embed) {
        $embed->title('Embed Title')
             ->description('This is the embed description')
             ->url('https://example.com')
             ->color('#3498db')
             ->timestamp() // Current time
             ->footer('Footer text', 'https://example.com/footer-icon.png')
             ->thumbnail('https://example.com/thumbnail.png')
             ->image('https://example.com/image.png')
             ->author('Author Name', 'https://example.com', 'https://example.com/author-icon.png')
             ->field('Field 1', 'Value 1', true)
             ->field('Field 2', 'Value 2', true)
             ->field('Field 3', 'Value 3', false);
    });
```

### Buttons

Add interactive buttons to your messages:

```php
NotifyCord::message('Message with buttons')
    ->button('Primary Button', 'primary', 'primary_button_id')
    ->button('Secondary Button', 'secondary', 'secondary_button_id')
    ->button('Success Button', 'success', 'success_button_id')
    ->button('Danger Button', 'danger', 'danger_button_id')
    ->button('Visit Website', 'link', null, 'https://example.com');
```

You can also organize buttons into multiple action rows:

```php
NotifyCord::message('Message with multiple action rows')
    ->button('Button 1', 'primary', 'button_1')
    ->button('Button 2', 'secondary', 'button_2')
    ->addActionRow()
    ->button('Button 3', 'success', 'button_3')
    ->button('Button 4', 'danger', 'button_4');
```

## Security

If you discover any security vulnerabilities, please email contact@example.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
