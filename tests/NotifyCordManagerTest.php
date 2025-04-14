<?php

namespace NotifyCord\NotifyCord\Tests;

use Illuminate\Contracts\Foundation\Application;
use NotifyCord\NotifyCord\DiscordMessage;
use NotifyCord\NotifyCord\NotifyCordManager;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class NotifyCordManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_create_a_discord_message()
    {
        $app = m::mock(Application::class);
        $app->shouldReceive('make')->andReturn(m::mock());

        // Mock the config values needed by the manager
        $app->shouldReceive('offsetGet')->with('config')->andReturn(m::mock([
            'get' => null,
        ]));

        $manager = new NotifyCordManager($app);
        $message = $manager->message('Hello, Discord!');

        $this->assertInstanceOf(DiscordMessage::class, $message);
        $this->assertEquals('Hello, Discord!', $message->toArray()['content']);
    }

    /** @test */
    public function it_can_determine_if_it_has_a_default_webhook()
    {
        $app = m::mock(Application::class);
        $app->shouldReceive('make')->andReturn(m::mock());

        // Mock config for testing
        $config = m::mock('config');
        $config->shouldReceive('get')
            ->with('notifycord.default_webhook', null)
            ->andReturn('https://discord.com/api/webhooks/123456789/abcdefghijk');

        $app->shouldReceive('offsetGet')->with('config')->andReturn($config);
        
        $manager = new NotifyCordManager($app);
        
        $this->assertTrue($manager->hasDefaultWebhook());
    }

    /** @test */
    public function it_returns_false_when_no_default_webhook()
    {
        $app = m::mock(Application::class);
        $app->shouldReceive('make')->andReturn(m::mock());

        // Mock config for testing
        $config = m::mock('config');
        $config->shouldReceive('get')
            ->with('notifycord.default_webhook', null)
            ->andReturn(null);

        $app->shouldReceive('offsetGet')->with('config')->andReturn($config);
        
        $manager = new NotifyCordManager($app);
        
        $this->assertFalse($manager->hasDefaultWebhook());
    }

    /** @test */
    public function it_can_get_retry_settings()
    {
        $app = m::mock(Application::class);
        $app->shouldReceive('make')->andReturn(m::mock());

        // Mock config for testing
        $config = [
            'retry_on_rate_limit' => true,
            'retry_on_failure' => true,
            'max_retries' => 5,
            'retry_delay' => 3,
        ];

        $configMock = m::mock('config');
        foreach ($config as $key => $value) {
            $configMock->shouldReceive('get')
                ->with("notifycord.{$key}", m::any())
                ->andReturn($value);
        }
        
        $configMock->shouldReceive('get')
            ->with(m::any(), m::any())
            ->andReturnNull();

        $app->shouldReceive('offsetGet')->with('config')->andReturn($configMock);
        
        $manager = new NotifyCordManager($app);
        
        $this->assertTrue($manager->shouldRetryOnRateLimit());
        $this->assertTrue($manager->shouldRetryOnFailure());
        $this->assertEquals(5, $manager->getMaxRetries());
        $this->assertEquals(3, $manager->getRetryDelay());
    }
}
