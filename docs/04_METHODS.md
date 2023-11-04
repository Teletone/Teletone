[To main documentation](00_MAIN.md)

# Methods

You can request any methods in telegram by accessing a bot instance

Example:

```php
$res = $bot->getMe();
echo $res->result->username;
```

The data is returned in a stdClass object. But sometimes an array representation can be useful. This can be done using the stdToArray function:

```php
$res = $bot->getMe();
$res = $bot->stdToArray($res);
echo $res['result']['username'];
```

Example send message:

```php
$bot->sendMessage([
    'chat_id' => '1234567890',
    'text' => 'Message'
]);
```

[Next chapter: Downloading files](05_DOWNLOADING.md)