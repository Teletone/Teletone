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

Research shows that very few people have the gift of generosity, so we won't ask for money anymore. Instead, write an email to teletone@skiff.com if you need a telegram (or discord) bot, and we can develop you any bot for your bucks!

# Testing

Run command:
```
composer run tests
```