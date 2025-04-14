<?php

namespace MehtaYukta\NotifyCord;

use JsonSerializable;

class DiscordMessage implements JsonSerializable
{
    /**
     * The message content.
     *
     * @var string
     */
    protected $content = '';

    /**
     * The message embeds.
     *
     * @var array
     */
    protected $embeds = [];

    /**
     * Whether the message should be read as TTS.
     *
     * @var bool
     */
    protected $tts = false;

    /**
     * The message thread name if creating a thread.
     *
     * @var string|null
     */
    protected $threadName = null;

    /**
     * The username to use when sending the message.
     *
     * @var string|null
     */
    protected $username = null;

    /**
     * The avatar URL to use when sending the message.
     *
     * @var string|null
     */
    protected $avatarUrl = null;

    /**
     * The components (buttons/menus) for the message.
     *
     * @var array
     */
    protected $components = [];

    /**
     * The number of times this message has been retried.
     *
     * @var int
     */
    protected $retryCount = 0;

    /**
     * Create a new message instance.
     *
     * @param string $content
     * @return void
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Create a new instance of the message.
     *
     * @param string $content
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }

    /**
     * Set the content of the message.
     *
     * @param string $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the message as a TTS message.
     *
     * @param bool $tts
     * @return $this
     */
    public function tts($tts = true)
    {
        $this->tts = $tts;

        return $this;
    }

    /**
     * Set the thread name to create a new thread from this message.
     *
     * @param string $threadName
     * @return $this
     */
    public function threadName($threadName)
    {
        $this->threadName = $threadName;

        return $this;
    }

    /**
     * Set the username to use when sending this message.
     *
     * @param string $username
     * @return $this
     */
    public function username($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set the avatar URL to use when sending this message.
     *
     * @param string $avatarUrl
     * @return $this
     */
    public function avatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * Add an embed to the message.
     *
     * @param array|callable $embed
     * @return $this
     */
    public function embed($embed)
    {
        if (is_callable($embed)) {
            $embedObject = new DiscordEmbed();
            $embed($embedObject);
            $this->embeds[] = $embedObject->toArray();
        } elseif (is_array($embed)) {
            $this->embeds[] = $embed;
        }

        return $this;
    }

    /**
     * Add a button component to the message.
     *
     * @param string $label
     * @param string $style
     * @param string|null $customId
     * @param string|null $url
     * @param bool $disabled
     * @return $this
     */
    public function button($label, $style = 'primary', $customId = null, $url = null, $disabled = false)
    {
        // Style mapping for Discord button styles
        $styleMap = [
            'primary' => 1,
            'secondary' => 2,
            'success' => 3,
            'danger' => 4,
            'link' => 5,
        ];

        $styleId = $styleMap[$style] ?? 1;
        
        // Create a row if none exists yet
        if (empty($this->components)) {
            $this->components[] = ['type' => 1, 'components' => []];
        }
        
        // Create button component
        $button = [
            'type' => 2,
            'label' => $label,
            'style' => $styleId,
            'disabled' => $disabled,
        ];
        
        // URL buttons (style 5) need a URL, others need a custom_id
        if ($styleId === 5 && $url) {
            $button['url'] = $url;
        } else {
            $button['custom_id'] = $customId ?? 'button_' . mt_rand(100000, 999999);
        }
        
        // Add button to the last action row
        $this->components[count($this->components) - 1]['components'][] = $button;
        
        return $this;
    }

    /**
     * Add a new action row for components.
     *
     * @return $this
     */
    public function addActionRow()
    {
        $this->components[] = ['type' => 1, 'components' => []];
        
        return $this;
    }

    /**
     * Increment the retry count for this message.
     *
     * @return $this
     */
    public function incrementRetryCount()
    {
        $this->retryCount++;
        
        return $this;
    }

    /**
     * Get the retry count for this message.
     *
     * @return int
     */
    public function getRetryCount()
    {
        return $this->retryCount;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'content' => $this->content,
            'tts' => $this->tts,
        ];

        if (! empty($this->embeds)) {
            $data['embeds'] = $this->embeds;
        }

        if (! empty($this->components)) {
            $data['components'] = $this->components;
        }

        if (! empty($this->username)) {
            $data['username'] = $this->username;
        }

        if (! empty($this->avatarUrl)) {
            $data['avatar_url'] = $this->avatarUrl;
        }

        if (! empty($this->threadName)) {
            $data['thread_name'] = $this->threadName;
        }

        return $data;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Serialize the message to an array.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

/**
 * Helper class for building Discord embeds.
 */
class DiscordEmbed
{
    /**
     * The embed title.
     *
     * @var string|null
     */
    protected $title = null;

    /**
     * The embed description.
     *
     * @var string|null
     */
    protected $description = null;

    /**
     * The embed URL.
     *
     * @var string|null
     */
    protected $url = null;

    /**
     * The embed timestamp.
     *
     * @var string|null
     */
    protected $timestamp = null;

    /**
     * The embed color.
     *
     * @var int|null
     */
    protected $color = null;

    /**
     * The embed footer.
     *
     * @var array|null
     */
    protected $footer = null;

    /**
     * The embed image.
     *
     * @var array|null
     */
    protected $image = null;

    /**
     * The embed thumbnail.
     *
     * @var array|null
     */
    protected $thumbnail = null;

    /**
     * The embed author.
     *
     * @var array|null
     */
    protected $author = null;

    /**
     * The embed fields.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Set the title of the embed.
     *
     * @param string $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the description of the embed.
     *
     * @param string $description
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the URL of the embed.
     *
     * @param string $url
     * @return $this
     */
    public function url($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set the timestamp of the embed.
     *
     * @param \DateTimeInterface|string|null $timestamp
     * @return $this
     */
    public function timestamp($timestamp = null)
    {
        if ($timestamp instanceof \DateTimeInterface) {
            $this->timestamp = $timestamp->format(\DateTimeInterface::ISO8601);
        } elseif (is_string($timestamp)) {
            $this->timestamp = $timestamp;
        } elseif ($timestamp === null) {
            $this->timestamp = date(\DateTimeInterface::ISO8601);
        }

        return $this;
    }

    /**
     * Set the color of the embed.
     *
     * @param int|string $color
     * @return $this
     */
    public function color($color)
    {
        if (is_string($color) && $color[0] === '#') {
            $color = hexdec(substr($color, 1));
        }

        $this->color = $color;

        return $this;
    }

    /**
     * Set the footer of the embed.
     *
     * @param string $text
     * @param string|null $iconUrl
     * @return $this
     */
    public function footer($text, $iconUrl = null)
    {
        $this->footer = [
            'text' => $text,
        ];

        if ($iconUrl !== null) {
            $this->footer['icon_url'] = $iconUrl;
        }

        return $this;
    }

    /**
     * Set the image of the embed.
     *
     * @param string $url
     * @return $this
     */
    public function image($url)
    {
        $this->image = [
            'url' => $url,
        ];

        return $this;
    }

    /**
     * Set the thumbnail of the embed.
     *
     * @param string $url
     * @return $this
     */
    public function thumbnail($url)
    {
        $this->thumbnail = [
            'url' => $url,
        ];

        return $this;
    }

    /**
     * Set the author of the embed.
     *
     * @param string $name
     * @param string|null $url
     * @param string|null $iconUrl
     * @return $this
     */
    public function author($name, $url = null, $iconUrl = null)
    {
        $this->author = [
            'name' => $name,
        ];

        if ($url !== null) {
            $this->author['url'] = $url;
        }

        if ($iconUrl !== null) {
            $this->author['icon_url'] = $iconUrl;
        }

        return $this;
    }

    /**
     * Add a field to the embed.
     *
     * @param string $name
     * @param string $value
     * @param bool $inline
     * @return $this
     */
    public function field($name, $value, $inline = false)
    {
        $this->fields[] = [
            'name' => $name,
            'value' => $value,
            'inline' => $inline,
        ];

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'timestamp' => $this->timestamp,
            'color' => $this->color,
            'footer' => $this->footer,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'author' => $this->author,
        ]);

        if (! empty($this->fields)) {
            $data['fields'] = $this->fields;
        }

        return $data;
    }
}
