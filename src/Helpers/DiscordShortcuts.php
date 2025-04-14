<?php

namespace NotifyCord\NotifyCord\Helpers;

use NotifyCord\NotifyCord\Facades\NotifyCord;

/**
 * Discord shortcut helper methods for common notification scenarios.
 */
class DiscordShortcuts
{
    /**
     * Send a success notification.
     *
     * @param string $title
     * @param string $message
     * @param array $fields
     * @param string|null $webhookUrl
     * @return bool
     */
    public static function success($title, $message, array $fields = [], $webhookUrl = null)
    {
        return self::prepareMessage($title, $message, '#2ecc71', $fields, $webhookUrl);
    }
    
    /**
     * Send an info notification.
     *
     * @param string $title
     * @param string $message
     * @param array $fields
     * @param string|null $webhookUrl
     * @return bool
     */
    public static function info($title, $message, array $fields = [], $webhookUrl = null)
    {
        return self::prepareMessage($title, $message, '#3498db', $fields, $webhookUrl);
    }
    
    /**
     * Send a warning notification.
     *
     * @param string $title
     * @param string $message
     * @param array $fields
     * @param string|null $webhookUrl
     * @return bool
     */
    public static function warning($title, $message, array $fields = [], $webhookUrl = null)
    {
        return self::prepareMessage($title, $message, '#f39c12', $fields, $webhookUrl);
    }
    
    /**
     * Send an error notification.
     *
     * @param string $title
     * @param string $message
     * @param array $fields
     * @param string|null $webhookUrl
     * @return bool
     */
    public static function error($title, $message, array $fields = [], $webhookUrl = null)
    {
        return self::prepareMessage($title, $message, '#e74c3c', $fields, $webhookUrl);
    }
    
    /**
     * Send an exception notification with stack trace.
     *
     * @param \Throwable $exception
     * @param string|null $webhookUrl
     * @return bool
     */
    public static function exception(\Throwable $exception, $webhookUrl = null)
    {
        $fields = [
            'Message' => $exception->getMessage(),
            'File' => $exception->getFile(),
            'Line' => $exception->getLine(),
            'Code' => $exception->getCode(),
        ];
        
        // Add basic stack trace (first 3 lines)
        $trace = array_slice($exception->getTrace(), 0, 3);
        $traceString = '';
        
        foreach ($trace as $i => $step) {
            $file = $step['file'] ?? 'unknown';
            $line = $step['line'] ?? 'unknown';
            $function = $step['function'] ?? 'unknown';
            $class = $step['class'] ?? '';
            $type = $step['type'] ?? '';
            
            $traceString .= "#{$i} {$file}({$line}): {$class}{$type}{$function}()\n";
        }
        
        $fields['Stack Trace'] = "```\n{$traceString}```";
        
        return self::prepareMessage(
            '🚨 Exception: ' . get_class($exception),
            $exception->getMessage(),
            '#e74c3c',
            $fields,
            $webhookUrl
        );
    }
    
    /**
     * Send a server status notification with system metrics.
     *
     * @param array $metrics
     * @param string|null $webhookUrl
     * @return bool
     */
    public static function serverStatus(array $metrics, $webhookUrl = null)
    {
        $fields = [];
        
        foreach ($metrics as $key => $value) {
            $fields[$key] = $value;
        }
        
        return self::prepareMessage(
            '🖥️ Server Status',
            'Current system metrics for ' . gethostname(),
            '#9b59b6',
            $fields,
            $webhookUrl
        );
    }
    
    /**
     * Send a deployment notification.
     *
     * @param string $environment
     * @param string $version
     * @param string $deployer
     * @param array $additionalFields
     * @param string|null $webhookUrl
     * @return bool
     */
    public static function deployment($environment, $version, $deployer, array $additionalFields = [], $webhookUrl = null)
    {
        $fields = [
            'Environment' => $environment,
            'Version' => $version,
            'Deployed by' => $deployer,
            'Date' => date('Y-m-d H:i:s'),
        ];
        
        $fields = array_merge($fields, $additionalFields);
        
        return self::prepareMessage(
            '🚀 Deployment Completed',
            "Application has been successfully deployed to {$environment}",
            '#3498db',
            $fields,
            $webhookUrl
        );
    }
    
    /**
     * Send a new signup notification.
     *
     * @param string $username
     * @param string $email
     * @param array $additionalFields
     * @param string|null $webhookUrl
     * @return bool
     */
    public static function newUser($username, $email, array $additionalFields = [], $webhookUrl = null)
    {
        $fields = [
            'Username' => $username,
            'Email' => $email,
            'Signup Date' => date('Y-m-d H:i:s'),
        ];
        
        $fields = array_merge($fields, $additionalFields);
        
        return self::prepareMessage(
            '👤 New User Registration',
            "A new user has registered on your platform",
            '#2ecc71',
            $fields,
            $webhookUrl
        );
    }
    
    /**
     * Send an order or transaction notification.
     *
     * @param string $orderNumber
     * @param string $amount
     * @param string $customer
     * @param array $additionalFields
     * @param string|null $webhookUrl
     * @return bool
     */
    public static function order($orderNumber, $amount, $customer, array $additionalFields = [], $webhookUrl = null)
    {
        $fields = [
            'Order Number' => $orderNumber,
            'Amount' => $amount,
            'Customer' => $customer,
            'Date' => date('Y-m-d H:i:s'),
        ];
        
        $fields = array_merge($fields, $additionalFields);
        
        return self::prepareMessage(
            '💰 New Order',
            "Order #{$orderNumber} has been received",
            '#f1c40f',
            $fields,
            $webhookUrl
        );
    }
    
    /**
     * Internal helper method to prepare and send a message.
     *
     * @param string $title
     * @param string $message
     * @param string $color
     * @param array $fields
     * @param string|null $webhookUrl
     * @return bool
     */
    private static function prepareMessage($title, $message, $color, array $fields, $webhookUrl = null)
    {
        return NotifyCord::sendMessage(
            $message,
            $webhookUrl,
            function($discordMessage) use ($title, $message, $color, $fields) {
                $discordMessage->embed(function($embed) use ($title, $message, $color, $fields) {
                    $embed->title($title)
                         ->description($message)
                         ->color($color)
                         ->timestamp();
                    
                    foreach ($fields as $name => $value) {
                        $embed->field((string) $name, (string) $value, true);
                    }
                });
            }
        );
    }
}