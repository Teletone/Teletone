[To main documentation](00_MAIN.md)

# Webhook

When you need to install a bot on a server, you need to use webhooks. Thank God it's easiest to do this in PHP

Your server must pass the correct certificate, such as letsencrypt. Allowed ports for wenhook: 80, 88, 443, 8443

After, use the setWebhook function. Pass the url of the entry point to the bot:

```php
$ret = $bot->setWebhook('https://example.com/bot.php');
echo json_encode($ret);
```

In the second parameter you can pass the parameters described here: https://core.telegram.org/bots/api#setwebhook

If everything went well you will see the message: Webhook was set