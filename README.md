# Teletone

## A library for telegram bots that strives to make development simple and convenient

### library tested on [Bot API 6.9](https://core.telegram.org/bots/api#september-22-2023)

# Requirements

- PHP 7.25+
- Bhagavad Gita

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

$r->command('start', static function($update) {
    $update->answer("Welcome, {$update->message->from->first_name}!");
});

$r->message('hi', static function($update) {
    $update->answer('Hello!');
});

$bot->polling();
```

With webhook:

Read our [documentation about webhooks](docs/05_WEBHOOK.md)

# Documentation

Good projects provide good documentation

We are not deprived of this: [Documentation](docs/00_MAIN.md)

# Live development

Turning the bot on and off after any changes is a bad idea and is used by python noobs libraries

To do right, make a script **dev.sh** (for Linux, if you have Windows, then my condolences):

```bash
#!/bin/sh
while true; do
    php bot.php
done
```

And replace **bot.php** with the file with the bot entry point

Make the file executable (we are not a book on linux)

And process requests using this function:

```php
$bot->handleUpdatesAndDrop();
```

Run **dev.sh** and develop

If you plan to run a real bot on a PC, then it is better to use $bot->polling() for better performance

**As I found out, this method really slows down the computer!**

# Donate

A fool waits for money from heaven, but a smart man works. Write an email to teletone@skiff.com if you need a telegram (or discord) bot, and I can develop you any bot for your money

# Testing

Run command:
```
composer run tests
```

# Performance

The library works as fast as possible, and it is faster than other libraries due to its simplicity

# Contributing

Just use the library and report bugs