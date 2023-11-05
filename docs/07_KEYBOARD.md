[To main documentation](00_MAIN.md)

# Keyboard

To send a keyboard you need to use reply_markup. We have convenient classes for it:

## ReplyKeyboardMarkup

Params:

- **$keyboard** - Array of keyboard arrays
- **$params** - [Extra params](https://core.telegram.org/bots/api#replykeyboardmarkup)

Example:

```php
use Teletone\Types\ReplyKeyboardMarkup;

$update->answer('hi', [
    'reply_markup' => new ReplyKeyboardMarkup([
        [ '1', '2', '3' ],
        [ '4', '5' ]
    ], [ 'resize_keyboard' => true ])
]);
```

## InlineKeyboardMarkup

Params:

- **$keyboard** - Array of keyboard arrays. This approach makes it easier to create dynamic keyboards
- **$params** - [Extra params](https://core.telegram.org/bots/api#inlinekeyboardmarkup)

Example:

```php
use Teletone\Types\InlineKeyboardMarkup;

$update->answer('hi', [
    'reply_markup' => new InlineKeyboardMarkup([
        [
            [
                'text' => '1',
                'callback_data' => 'click 1'
            ].
            [
                'text' => '2',
                'callback_data' => 'click 2'
            ].
        ],
        [
            [
                'text' => '3',
                'callback_data' => 'click 3'
            ].
        ]
    ])
]);
```

### Get JSON data

When passing the class, the getJSON method is called, you can call it manually to get the json representation:

```php
echo (new ReplyKeyboardMarkup([
    [ '1', '2', '3' ],
    [ '4', '5' ]
], [ 'resize_keyboard' => true ]))->getJSON();
```

## ForceReply

Show the user a reply form to a message

Params:

- **$params** - [Extra params](https://core.telegram.org/bots/api#forcereply)

Example:

```php
use Teletone\Types\ForceReply;

$update->answer('hi', [
    'reply_markup' => new ForceReply()
]);
```

## ReplyKeyboardRemove

Removes the reply keyboard

Params:

- **$params** - [Extra params](https://core.telegram.org/bots/api#replykeyboardremove)

Example:

```php
use Teletone\Types\ReplyKeyboardRemove;

$update->answer('hi', [
    'reply_markup' => new ReplyKeyboardRemove()
]);
```

[Next chapter: Bot on classes](08_CLASSES.md)