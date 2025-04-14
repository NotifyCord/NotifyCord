<?php

namespace NotifyCord\NotifyCord\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use NotifyCord\NotifyCord\DiscordChannel;
use NotifyCord\NotifyCord\DiscordMessage;
use NotifyCord\NotifyCord\Exceptions\CouldNotSendNotification;
use NotifyCord\NotifyCord\NotifyCordManager;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DiscordChannelTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_send_a_notification_with_string_message()
    {
        $notification = new TestNotificationWithStringMessage();
        $notifiable = new TestNotifiable();

        $mock = new MockHandler([
            new Response(200),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $manager = m::mock(NotifyCordManager::class);
        $manager->shouldReceive('shouldRetryOnRateLimit')->andReturn(false);
        $manager->shouldReceive('shouldRetryOnFailure')->andReturn(false);
        $manager->shouldReceive('shouldLogErrors')->andReturn(false);

        $channel = new DiscordChannel($client, $manager);

        $response = $channel->send($notifiable, $notification);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_can_send_a_notification_with_discord_message()
    {
        $notification = new TestNotificationWithDiscordMessage();
        $notifiable = new TestNotifiable();

        $mock = new MockHandler([
            new Response(200),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $manager = m::mock(NotifyCordManager::class);
        $manager->shouldReceive('shouldRetryOnRateLimit')->andReturn(false);
        $manager->shouldReceive('shouldRetryOnFailure')->andReturn(false);
        $manager->shouldReceive('shouldLogErrors')->andReturn(false);

        $channel = new DiscordChannel($client, $manager);

        $response = $channel->send($notifiable, $notification);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_throws_an_exception_when_invalid_message_object_returned()
    {
        $notification = new TestNotificationWithInvalidMessage();
        $notifiable = new TestNotifiable();

        $client = m::mock(Client::class);
        $manager = m::mock(NotifyCordManager::class);

        $channel = new DiscordChannel($client, $manager);

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Discord notification must return a `NotifyCord\NotifyCord\DiscordMessage` instance.');

        $channel->send($notifiable, $notification);
    }

    /** @test */
    public function it_handles_rate_limits()
    {
        $notification = new TestNotificationWithDiscordMessage();
        $notifiable = new TestNotifiable();

        $mock = new MockHandler([
            new Response(429, ['Retry-After' => '2']),
            new Response(200),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $manager = m::mock(NotifyCordManager::class);
        $manager->shouldReceive('shouldRetryOnRateLimit')->andReturn(true);
        $manager->shouldReceive('shouldRetryOnFailure')->andReturn(false);
        $manager->shouldReceive('shouldLogErrors')->andReturn(false);

        $channel = new DiscordChannel($client, $manager);

        $response = $channel->send($notifiable, $notification);

        $this->assertEquals(200, $response->getStatusCode());
    }
}

class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForDiscord()
    {
        return 'https://discord.com/api/webhooks/123456789/abcdefghijk';
    }
}

class TestNotificationWithStringMessage extends Notification
{
    public function toDiscord($notifiable)
    {
        return 'Hello, Discord!';
    }
}

class TestNotificationWithDiscordMessage extends Notification
{
    public function toDiscord($notifiable)
    {
        return (new DiscordMessage())
            ->content('Hello, Discord!')
            ->embed(function ($embed) {
                $embed->title('Test Title')
                    ->description('This is a test description')
                    ->color('#ff0000');
            });
    }
}

class TestNotificationWithInvalidMessage extends Notification
{
    public function toDiscord($notifiable)
    {
        return [
            'content' => 'Hello, Discord!',
        ];
    }
}
