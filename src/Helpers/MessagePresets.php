<?php

namespace NotifyCord\NotifyCord\Helpers;

use NotifyCord\NotifyCord\DiscordMessage;

/**
 * Predefined message templates and styles for common notification types.
 */
class MessagePresets
{
    /**
     * Create a success message preset.
     *
     * @param string $title
     * @param string $message
     * @param array $fields
     * @return \Closure
     */
    public static function success($title, $message, array $fields = [])
    {
        return function (DiscordMessage $discordMessage) use ($title, $message, $fields) {
            return $discordMessage->embed(function ($embed) use ($title, $message, $fields) {
                $embed->title('✅ ' . $title)
                     ->description($message)
                     ->color('#2ecc71') // Green
                     ->timestamp();
                
                foreach ($fields as $name => $value) {
                    $embed->field($name, $value, true);
                }
            });
        };
    }
    
    /**
     * Create an error message preset.
     *
     * @param string $title
     * @param string $message
     * @param array $fields
     * @return \Closure
     */
    public static function error($title, $message, array $fields = [])
    {
        return function (DiscordMessage $discordMessage) use ($title, $message, $fields) {
            return $discordMessage->embed(function ($embed) use ($title, $message, $fields) {
                $embed->title('❌ ' . $title)
                     ->description($message)
                     ->color('#e74c3c') // Red
                     ->timestamp();
                
                foreach ($fields as $name => $value) {
                    $embed->field($name, $value, true);
                }
            });
        };
    }
    
    /**
     * Create a warning message preset.
     *
     * @param string $title
     * @param string $message
     * @param array $fields
     * @return \Closure
     */
    public static function warning($title, $message, array $fields = [])
    {
        return function (DiscordMessage $discordMessage) use ($title, $message, $fields) {
            return $discordMessage->embed(function ($embed) use ($title, $message, $fields) {
                $embed->title('⚠️ ' . $title)
                     ->description($message)
                     ->color('#f39c12') // Yellow/Orange
                     ->timestamp();
                
                foreach ($fields as $name => $value) {
                    $embed->field($name, $value, true);
                }
            });
        };
    }
    
    /**
     * Create an info message preset.
     *
     * @param string $title
     * @param string $message
     * @param array $fields
     * @return \Closure
     */
    public static function info($title, $message, array $fields = [])
    {
        return function (DiscordMessage $discordMessage) use ($title, $message, $fields) {
            return $discordMessage->embed(function ($embed) use ($title, $message, $fields) {
                $embed->title('ℹ️ ' . $title)
                     ->description($message)
                     ->color('#3498db') // Blue
                     ->timestamp();
                
                foreach ($fields as $name => $value) {
                    $embed->field($name, $value, true);
                }
            });
        };
    }
    
    /**
     * Create a server alert preset (for system monitoring).
     *
     * @param string $title
     * @param string $message
     * @param array $metrics
     * @return \Closure
     */
    public static function serverAlert($title, $message, array $metrics = [])
    {
        return function (DiscordMessage $discordMessage) use ($title, $message, $metrics) {
            return $discordMessage->embed(function ($embed) use ($title, $message, $metrics) {
                $embed->title('🖥️ ' . $title)
                     ->description($message)
                     ->color('#9b59b6') // Purple
                     ->timestamp();
                
                foreach ($metrics as $name => $value) {
                    $embed->field($name, $value, true);
                }
                
                $embed->footer('Server Monitor', 'https://cdn.discordapp.com/emojis/780036584169445377.png');
            });
        };
    }
    
    /**
     * Create a user activity preset.
     *
     * @param string $title
     * @param string $message
     * @param string $username
     * @param string $userAvatar
     * @param array $activityDetails
     * @return \Closure
     */
    public static function userActivity($title, $message, $username, $userAvatar = null, array $activityDetails = [])
    {
        return function (DiscordMessage $discordMessage) use ($title, $message, $username, $userAvatar, $activityDetails) {
            return $discordMessage->embed(function ($embed) use ($title, $message, $username, $userAvatar, $activityDetails) {
                $embed->title('👤 ' . $title)
                     ->description($message)
                     ->color('#1abc9c') // Teal
                     ->timestamp();
                
                if ($userAvatar) {
                    $embed->author($username, null, $userAvatar);
                } else {
                    $embed->author($username);
                }
                
                foreach ($activityDetails as $name => $value) {
                    $embed->field($name, $value, true);
                }
            });
        };
    }
}

    /**
     * Create a payment notification preset.
     *
     * @param string $status
     * @param array $paymentDetails
     * @return \Closure
     */
    public static function payment($status, array $paymentDetails)
    {
        return function (DiscordMessage $discordMessage) use ($status, $paymentDetails) {
            return $discordMessage->embed(function ($embed) use ($status, $paymentDetails) {
                $embed->title('💳 Payment ' . ucfirst($status))
                     ->color($status === 'success' ? '#2ecc71' : '#e74c3c')
                     ->timestamp();
                
                foreach ($paymentDetails as $name => $value) {
                    $embed->field($name, $value, true);
                }
            });
        };
    }

    /**
     * Create an API status update preset.
     *
     * @param string $status
     * @param string $message
     * @param array $metrics
     * @return \Closure
     */
    public static function apiStatus($status, $message, array $metrics = [])
    {
        return function (DiscordMessage $discordMessage) use ($status, $message, $metrics) {
            return $discordMessage->embed(function ($embed) use ($status, $message, $metrics) {
                $embed->title('🔌 API Status Update')
                     ->description($message)
                     ->color($status === 'up' ? '#2ecc71' : '#e74c3c')
                     ->timestamp();
                
                foreach ($metrics as $name => $value) {
                    $embed->field($name, $value, true);
                }
            });
        };
    }

    /**
     * Create a security alert preset.
     *
     * @param string $level
     * @param string $message
     * @param array $details
     * @return \Closure
     */
    public static function securityAlert($level, $message, array $details = [])
    {
        $colors = [
            'critical' => '#e74c3c',
            'high' => '#e67e22',
            'medium' => '#f1c40f',
            'low' => '#3498db'
        ];

        return function (DiscordMessage $discordMessage) use ($level, $message, $details, $colors) {
            return $discordMessage->embed(function ($embed) use ($level, $message, $details, $colors) {
                $embed->title('🛡️ Security Alert: ' . ucfirst($level))
                     ->description($message)
                     ->color($colors[$level] ?? '#95a5a6')
                     ->timestamp();
                
                foreach ($details as $name => $value) {
                    $embed->field($name, $value, false);
                }
            });
        };
    }
