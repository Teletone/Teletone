# Routing

Our library works on the principle of routing. You set the necessary directives for processing and specify the handler, then when an event occurs it runs the handler with the $update parameter

To add routes you need to get a router:

```php
$r = $bot->getRouter();
````

Then use the directive functions to set the handlers

## message

Fires when a message is received

Params:

- **text** - Text or regular expression to check. If NULL, fires on any message
- **callback** - Сallback for call
- **regex** - Sets true to check message by regex, then text should be a regular expression
- **types** - Types from Telethon\Types for which the callback will be called

Examples:

```php
// When message is photo
$r->message(NULL, static function($update) {
    $update->answer('is a photo');
}, false, Teletone\Types::PHOTO);

// When message is number
$r->message('/[0-9]+/', static function($update) {
    $update->answer('is a number');
}, true);

// Any message
$r->message(NULL, static function($update) {
    $update->answer('Your message: '.$update->message->text);
});
```

## command

Fires when the command is called

Params:

- **text** - Command text
- **callback** - Сallback for call
- **regex** - Sets true to check command by regex, then text should be a regular expression

Examples:

```php
// Use: /num
$r->command('num', static function($update) {
    $update->answer(mt_rand(1, 100));
});

// Use: /num 12345
$r->command('/^num [0-9]+$/', static function($update) {
    $num = explode(' ', $update->message->text)[1];
    $update->answer('Num: '.$num);
}, true);
```

## any

Triggers on any event

Params:

- **callback** - Сallback for call

Examples:

```php
$r->any(static function($update) {
    var_dump($update->asArray());
});
```

## callbackQuery

Fires when the inline button is clicked

Params:

- **text** - callback_data text
- **callback** - Сallback for call

Examples:

```php
$r->callbackQuery('click', static function($update) {
    $update->answer('clicked', [
        'show_alert' => true
    ]);
});
```

## chatMember

Triggers when entering/exiting a bot, group, community

Params:

- **callback** - Сallback for call
- **statuses** - Listing the statuses Teletone\Statuses for which the callback will be called

Examples:

```php
$r->chatMember(static function($update) {
    echo 'new member: '.$update->my_chat_member->from->id;
}, Teletone\Statuses::MEMBER);
```