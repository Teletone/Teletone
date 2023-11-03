[To main documentation](00_MAIN.md)

# Creating a bot

So we want to make a telegram bot!

To give birth to a new bot we need a bot [@BotFather](https://t.me/BotFather)

Launch the bot and enter the command /newbot:

![BotFatherNewbot](https://i.imgur.com/xUtd0zm.png)

Enter your bot's name

After this you need to set the username of the bot:

![BotFatherSetUsername](https://i.imgur.com/chv9rLg.png)

Create a username and put **bot** at the end

After, a window will appear indicating the successful creation of the bot and the secret bot token:

![BotFatherSetUsername](https://i.imgur.com/nZekPT9.png)

Click on the token that hide is colored pink, you will need it later

_________________

Install Teletone using composer:

`composer require teletone/teletone`

Now let's connect autoload:

```php
require 'vendor/autoload.php';
```

Now you can create a bot:

```php
$bot = new Teletone\Bot('YOUR BOT TOKEN');
```

Paste your token there

The following options can be passed to the second parameter:

- **parse_mode** - sets the parsing mode for all outgoing requests. Possible values: html, MarkdownV2, Markdown. Nothing is installed by default
- **debug** - Set to true to receive debug messages to the console

Example:

```php
$bot = new Teletone\Bot('YOUR BOT TOKEN', [
    'parse_mode' => 'html',
    'debug' => true
]);
```

_________________

[Next chapter: Routing](02_ROUTING.md)