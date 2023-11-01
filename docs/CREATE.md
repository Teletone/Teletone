# Creating a bot

First install our project using composer:

`composer require teletone/teletone`

Now let's connect autoload:

```php
require 'vendor/autoload.php';
```

Now you can create a bot:

```php
$bot = new Teletone\Bot($bot_token);
```

The following options can be passed to the second parameter:

- **parse_mode** - sets the parsing mode for all outgoing requests. Possible values: html, MarkdownV2, Markdown. Nothing is installed by default
- **debug** - Set to true to receive debug messages to the console

Example:

```php
$bot = new Teletone\Bot($bot_token, [
    'parse_mode' => 'html',
    'debug' => true
]);
```