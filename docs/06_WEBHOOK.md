[To main documentation](00_MAIN.md)

# Webhook

When you need to install a bot on a server, you need to use webhooks. Thank God it's easiest to do this in PHP

Your server must pass the correct certificate, such as letsencrypt or self-signed certificate. Allowed ports for wenhook: 80, 88, 443, 8443

After, use the setWebhook function. Pass the url of the entry point to the bot. In the second parameter you can pass the parameters described here: https://core.telegram.org/bots/api#setwebhook

```php
$ret = $bot->setWebhook('https://example.com/bot.php');
echo json_encode($ret);
```

You can use a self-signed certificate:

```php
$bot->setWebhook('https://example.com/bot.php', [
    'certificate' => new Teletone\Types\InputFile('certificate.crt')
]);
```

If everything went well you will see the message: **Webhook was set**

To remove a hook, call the method:

```php
$bot->deleteWebhook();
```

The function takes one parameter $drop_pending_updates, set to true to discard pending updates

Update processing works using the function:

```php
$bot->handleWebhook();
```

It is also useful to include logs in the file:

```php
$bot = new Teletone\Bot($bot_token, [
    'debug' => true,
    'debug_in_file' => 'debug.log'
]);
```

## Safety

By default, the first parameter for checking ip is true. This means the ip will be checked automatically against the official telegram ip (subnets 149.154.160.0/20 and 91.108.4.0/22), this also takes into account if you have Cloudflare protection. IP is taken from `$_SERVER['HTTP_CF_CONNECTING_IP']` or `$_SERVER['REMOTE_ADDR']`

You can disable the check:

```php
$bot->handleWebhook(false);
```

If problems arise, you can get the ip yourself and specify it in the second parameter:

```php
$bot->handleWebhook(true, $ip);
```

The function returns false if the ip is not allowed, otherwise true and performs processing

[Next chapter: Keyboard](07_KEYBOARD.md)