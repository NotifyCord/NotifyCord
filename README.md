<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/notifycord-logo.png" alt="NotifyCord Logo" width="180">
</p>

<h1 align="center">NotifyCord</h1>

<p align="center">
  A powerful and flexible Discord notification package for Laravel
</p>

<p align="center">
  <a href="https://packagist.org/packages/notifycord/notifycord"><img src="https://img.shields.io/packagist/v/notifycord/notifycord.svg?style=for-the-badge" alt="Latest Version on Packagist"></a>
  <a href="https://packagist.org/packages/notifycord/notifycord"><img src="https://img.shields.io/packagist/dt/notifycord/notifycord.svg?style=for-the-badge" alt="Total Downloads"></a>
  <a href="https://github.com/NotifyCord/NotifyCord"><img src="https://img.shields.io/badge/github-NotifyCord%2FNotifyCord-blue?style=for-the-badge" alt="GitHub Repository"></a>
  <a href="https://github.com/NotifyCord/NotifyCord/blob/main/LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-green?style=for-the-badge" alt="License"></a>
</p>

<p align="center">
NotifyCord is a powerful and flexible package for sending Discord notifications from your Laravel application. It provides a clean and simple way to send rich Discord messages through channels and webhooks, and integrates perfectly with Laravel's notification system.
</p>

---

## ✨ Features

- 🔌 Seamless integration with Laravel's Notification system
- 🔗 Support for webhook-based notifications
- 📋 Rich embed message support (title, description, fields, colors, etc.)
- 🔘 Discord components support (buttons, action rows)
- 🔁 Automatic rate limit handling and retries
- 🧩 Queue-compatible for asynchronous notifications
- 🛡️ Error handling with detailed exceptions
- ⚙️ Comprehensive configuration options

## 📋 Requirements

- PHP 8.0 or higher
- Laravel 9.0 or higher
- GuzzleHTTP 7.0 or higher

## 💻 Installation

<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/installation.png" alt="Installation" width="500">
</p>

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

### Publishing the Configuration

You can publish the configuration file with:

```bash
php artisan vendor:publish --tag="notifycord-config"
```

This will create a `config/notifycord.php` configuration file in your application with the following options:

```php
return [
    'default_webhook' => env('DISCORD_WEBHOOK_URL'),
    // 'bot_token' => env('DISCORD_BOT_TOKEN'),  // Not used in webhook-only mode
    'retry_on_rate_limit' => true,
    'retry_on_failure' => true,
    'max_retries' => 3,
    'retry_delay' => 2, // seconds
    'timeout' => 5, // seconds
    'connect_timeout' => 5, // seconds
    'log_errors' => true,
    'log_file' => storage_path('logs/notifycord.log'),  // Custom log file for NotifyCord
    // ...
];
```

### Environment Configuration

Add the following to your `.env` file:

```
DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/webhook-id/webhook-token
```

## 📖 Usage Guide

<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/notifycord-banner.png" alt="NotifyCord Banner" width="800">
</p>

### 🚀 Laravel Notification System Integration

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
        // Return your Discord webhook URL
        return 'https://discord.com/api/webhooks/your-webhook-id/your-webhook-token';
    }
}
```

Then send the notification:

```php
$user->notify(new DiscordNotification('Hello from NotifyCord!'));
```

### 🔄 Direct Usage

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

## 📊 Message Components

<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/discord-components.png" alt="Discord Components" width="700">
</p>

### 🎨 Rich Embeds

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

### 🔘 Interactive Buttons

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

## 📋 Examples

<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/example-preview.png" alt="Example Preview" width="650">
</p>

### 📱 Mobile App Notifications

```php
// Mobile app transaction notification
public function sendPurchaseNotification($user, $transaction)
{
    $user->notify(new DiscordNotification("New Purchase: #{$transaction->id}"))
        ->embed(function ($embed) use ($transaction) {
            $embed->title("Purchase: {$transaction->product_name}")
                 ->description("Your purchase has been successfully completed.")
                 ->timestamp()
                 ->color('#2ecc71')
                 ->field('Customer', $transaction->user->name, true)
                 ->field('Price', "{$transaction->amount} {$transaction->currency}", true)
                 ->field('Status', 'Confirmed', true);
        })
        ->button('Order Details', 'primary', 'view_order')
        ->button('Invoice', 'secondary', 'view_invoice')
        ->button('Support', 'link', null, 'https://example.com/support');
}
```

### 🚨 System Alerts

```php
// System alert notification
public function sendServerAlert($system, $metrics)
{
    return NotifyCord::message("System Alert: {$system->name}")
        ->embed(function ($embed) use ($system, $metrics) {
            $embed->title("🚨 High CPU Usage")
                 ->description("Server CPU usage has exceeded the configured threshold.")
                 ->color('#e74c3c')
                 ->timestamp()
                 ->field('Server', $system->name, true)
                 ->field('CPU', "{$metrics->cpu_usage}%", true)
                 ->field('Memory', "{$metrics->memory_usage}%", true)
                 ->field('Disk', "{$metrics->disk_usage}%", true)
                 ->footer("Server Monitor", "https://example.com/logo.png");
        });
}
```

## 🐛 Debugging and Troubleshooting

If your messages are not being sent but no errors appear, you can check the NotifyCord log file:

```php
$logPath = storage_path('logs/notifycord.log');
if (file_exists($logPath)) {
    $logs = file_get_contents($logPath);
    // Display or process logs
}
```

Common issues and solutions:

1. **Invalid webhook URL**: Ensure your webhook URL is correct and the webhook exists in your Discord server.
2. **Rate limiting**: Discord may rate limit your requests. The package will retry automatically, but check logs for details.
3. **Network issues**: If your server cannot reach Discord's API, check your server's network configuration.
4. **Large messages**: Discord has message size limits. Try sending smaller messages or fewer embeds.

You can also enable verbose logging by adding this to your `.env` file:

```
NOTIFYCORD_DEBUG=true
```

## 🛡️ Security

If you discover any security vulnerabilities, please email contact@example.com instead of using the issue tracker.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.
