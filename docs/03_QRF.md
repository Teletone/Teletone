[To main documentation](00_MAIN.md)

# Quick Reply Features

In each event the callback receives $update

It has handy features for replies and more

## answer

Allows you to answer to a message or callback

- **text** - Text
- **params** - An array with parameters that [can be viewed here](https://core.telegram.org/bots/api#sendmessage)

Example:

```php
$update->answer('hello', [
    'disable_notification' => true
]);
```

## reply

Allows you to reply to a message or callback

- **text** - Text
- **params** - An array with parameters that [can be viewed here](https://core.telegram.org/bots/api#sendmessage)

Example:

```php
$update->reply('hello', [
    'protect_content' => true
]);
```

