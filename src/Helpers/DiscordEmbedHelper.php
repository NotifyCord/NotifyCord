<?php

namespace NotifyCord\NotifyCord\Helpers;

/**
 * Helper class for custom embed generators.
 */
class DiscordEmbedHelper
{
    /**
     * Create a thumbnail-only embed.
     *
     * @param string $title
     * @param string $thumbnailUrl
     * @param string $color
     * @return \Closure
     */
    public static function thumbnailEmbed($title, $thumbnailUrl, $color = '#3498db')
    {
        return function ($embed) use ($title, $thumbnailUrl, $color) {
            $embed->title($title)
                 ->color($color)
                 ->thumbnail($thumbnailUrl);
        };
    }
    
    /**
     * Create an image-only embed.
     *
     * @param string $title
     * @param string $imageUrl
     * @param string $color
     * @return \Closure
     */
    public static function imageEmbed($title, $imageUrl, $color = '#3498db')
    {
        return function ($embed) use ($title, $imageUrl, $color) {
            $embed->title($title)
                 ->color($color)
                 ->image($imageUrl);
        };
    }
    
    /**
     * Create a code snippet embed.
     *
     * @param string $title
     * @param string $language
     * @param string $code
     * @param string $color
     * @return \Closure
     */
    public static function codeEmbed($title, $language, $code, $color = '#2c3e50')
    {
        return function ($embed) use ($title, $language, $code, $color) {
            $embed->title($title)
                 ->description("```$language\n$code\n```")
                 ->color($color);
        };
    }
    
    /**
     * Create a link embed with author information.
     *
     * @param string $title
     * @param string $url
     * @param string $description
     * @param string $authorName
     * @param string|null $authorUrl
     * @param string|null $authorIcon
     * @param string $color
     * @return \Closure
     */
    public static function linkEmbed($title, $url, $description, $authorName, $authorUrl = null, $authorIcon = null, $color = '#3498db')
    {
        return function ($embed) use ($title, $url, $description, $authorName, $authorUrl, $authorIcon, $color) {
            $embed->title($title)
                 ->url($url)
                 ->description($description)
                 ->color($color)
                 ->timestamp();
                 
            if ($authorName) {
                $embed->author($authorName, $authorUrl, $authorIcon);
            }
        };
    }
    
    /**
     * Create a progress bar embed.
     *
     * @param string $title
     * @param int $current
     * @param int $total
     * @param string $label
     * @param string $color
     * @return \Closure
     */
    public static function progressEmbed($title, $current, $total, $label = 'Progress', $color = '#3498db')
    {
        return function ($embed) use ($title, $current, $total, $label, $color) {
            $percentage = min(100, round(($current / max(1, $total)) * 100));
            $progressBars = [
                '▰', '▱' // Alternative: '█', '░'
            ];
            
            $barCount = 20; // Length of progress bar
            $filledCount = round($percentage / 100 * $barCount);
            $emptyCount = $barCount - $filledCount;
            
            $progressBar = str_repeat($progressBars[0], $filledCount) . str_repeat($progressBars[1], $emptyCount);
            
            $embed->title($title)
                 ->description("**$label:** $current / $total\n$progressBar ($percentage%)")
                 ->color($color)
                 ->timestamp();
        };
    }
    
    /**
     * Create a comparison embed with before/after fields.
     *
     * @param string $title
     * @param array $comparisons Key-value pairs of before => after
     * @param string $color
     * @return \Closure
     */
    public static function comparisonEmbed($title, array $comparisons, $color = '#3498db')
    {
        return function ($embed) use ($title, $comparisons, $color) {
            $embed->title($title)
                 ->color($color)
                 ->timestamp();
                 
            foreach ($comparisons as $name => $values) {
                $embed->field(
                    $name,
                    "**Before:** {$values['before']}\n**After:** {$values['after']}",
                    false
                );
            }
        };
    }
}