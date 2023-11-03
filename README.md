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

$r->message('hi', static function($update) {
    $update->answer('hello');
});

$bot->polling();
```

With webhook:

Use this instead of $bot->polling():

```php
$bot->handleWebhook();
```

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

# Contributing

Just use the library and report bugs