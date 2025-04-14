<?php

namespace NotifyCord\NotifyCord\Tests;

use NotifyCord\NotifyCord\DiscordMessage;
use PHPUnit\Framework\TestCase;

class DiscordMessageTest extends TestCase
{
    /** @test */
    public function it_can_create_a_message_with_content()
    {
        $message = DiscordMessage::create('Hello, Discord!');

        $this->assertEquals([
            'content' => 'Hello, Discord!',
            'tts' => false,
        ], $message->toArray());
    }

    /** @test */
    public function it_can_set_tts()
    {
        $message = DiscordMessage::create('Hello, Discord!')
            ->tts(true);

        $this->assertEquals([
            'content' => 'Hello, Discord!',
            'tts' => true,
        ], $message->toArray());
    }

    /** @test */
    public function it_can_add_an_embed()
    {
        $message = DiscordMessage::create('Hello, Discord!')
            ->embed(function ($embed) {
                $embed->title('Test Title')
                    ->description('This is a test description')
                    ->color('#ff0000');
            });

        $expected = [
            'content' => 'Hello, Discord!',
            'tts' => false,
            'embeds' => [
                [
                    'title' => 'Test Title',
                    'description' => 'This is a test description',
                    'color' => 16711680,
                ],
            ],
        ];

        $this->assertEquals($expected, $message->toArray());
    }

    /** @test */
    public function it_can_add_a_button()
    {
        $message = DiscordMessage::create('Hello, Discord!')
            ->button('Click Me', 'primary', 'custom_id_123');

        $array = $message->toArray();
        
        $this->assertArrayHasKey('components', $array);
        $this->assertEquals(1, count($array['components']));
        $this->assertEquals(1, $array['components'][0]['type']);
        $this->assertEquals('Click Me', $array['components'][0]['components'][0]['label']);
        $this->assertEquals(1, $array['components'][0]['components'][0]['style']);
        $this->assertEquals('custom_id_123', $array['components'][0]['components'][0]['custom_id']);
    }

    /** @test */
    public function it_can_add_a_link_button()
    {
        $message = DiscordMessage::create('Hello, Discord!')
            ->button('Visit Website', 'link', null, 'https://example.com');

        $array = $message->toArray();
        
        $this->assertArrayHasKey('components', $array);
        $this->assertEquals(1, count($array['components']));
        $this->assertEquals('Visit Website', $array['components'][0]['components'][0]['label']);
        $this->assertEquals(5, $array['components'][0]['components'][0]['style']);
        $this->assertEquals('https://example.com', $array['components'][0]['components'][0]['url']);
    }

    /** @test */
    public function it_can_set_username_and_avatar()
    {
        $message = DiscordMessage::create('Hello, Discord!')
            ->username('TestBot')
            ->avatarUrl('https://example.com/avatar.png');

        $expected = [
            'content' => 'Hello, Discord!',
            'tts' => false,
            'username' => 'TestBot',
            'avatar_url' => 'https://example.com/avatar.png',
        ];

        $this->assertEquals($expected, $message->toArray());
    }

    /** @test */
    public function it_can_create_a_thread()
    {
        $message = DiscordMessage::create('Hello, Discord!')
            ->threadName('Test Thread');

        $expected = [
            'content' => 'Hello, Discord!',
            'tts' => false,
            'thread_name' => 'Test Thread',
        ];

        $this->assertEquals($expected, $message->toArray());
    }

    /** @test */
    public function it_can_add_multiple_embeds()
    {
        $message = DiscordMessage::create('Hello, Discord!')
            ->embed(function ($embed) {
                $embed->title('First Embed')
                    ->description('This is the first embed');
            })
            ->embed(function ($embed) {
                $embed->title('Second Embed')
                    ->description('This is the second embed');
            });

        $array = $message->toArray();
        
        $this->assertArrayHasKey('embeds', $array);
        $this->assertEquals(2, count($array['embeds']));
        $this->assertEquals('First Embed', $array['embeds'][0]['title']);
        $this->assertEquals('Second Embed', $array['embeds'][1]['title']);
    }

    /** @test */
    public function it_can_add_embed_fields()
    {
        $message = DiscordMessage::create('Hello, Discord!')
            ->embed(function ($embed) {
                $embed->title('Embed with Fields')
                    ->field('Field 1', 'Value 1', true)
                    ->field('Field 2', 'Value 2', false);
            });

        $array = $message->toArray();
        
        $this->assertArrayHasKey('embeds', $array);
        $this->assertArrayHasKey('fields', $array['embeds'][0]);
        $this->assertEquals(2, count($array['embeds'][0]['fields']));
        $this->assertEquals('Field 1', $array['embeds'][0]['fields'][0]['name']);
        $this->assertEquals('Value 1', $array['embeds'][0]['fields'][0]['value']);
        $this->assertTrue($array['embeds'][0]['fields'][0]['inline']);
        $this->assertEquals('Field 2', $array['embeds'][0]['fields'][1]['name']);
        $this->assertFalse($array['embeds'][0]['fields'][1]['inline']);
    }
}
