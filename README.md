# NotifyCord

A powerful and flexible package for sending Discord notifications from your Laravel application. NotifyCord provides a clean and simple way to send rich Discord messages through channels, webhooks, and integrates perfectly with Laravel's notification system.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mehtayukta/notifycord.svg?style=flat-square)](https://packagist.org/packages/mehtayukta/notifycord)
[![Total Downloads](https://img.shields.io/packagist/dt/mehtayukta/notifycord.svg?style=flat-square)](https://packagist.org/packages/mehtayukta/notifycord)

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
composer require mehtayukta/notifycord
