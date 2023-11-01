# Teletone

## The library is under development

## A library for telegram bots that strives to make development simple and convenient

# Requirements

- PHP 7.25+
- Straight arms

# Install

`composer require teletone/teletone`

# The simplest bot

It's amazingly simple:

```php
<?php

require 'vendor/autoload.php';

$bot_token = 'YOUR_BOT_TOKEN';

$bot = new Teletone\Bot($bot_token);
$r = $bot->getRouter();

$r->command('test', static function($update) {
    $update->answer('ok');
});

$r->message(NULL, static function($update) {
    $update->answer('hello');
});

$bot->polling();
```

# Documentation

Good projects provide good documentation

We are not deprived of this: [Documentation](docs/MAIN.md)

# Donate

Although the library looks simple, it took a lot of experience in developing bots in different languages and many months to develop it. If you like the library you can support me, and if not I just did complete crap

Paypal, Cards and etc:
[Donate with Boosty](https://boosty.to/teletone)

# Testing

Run command:
```
composer run tests
```