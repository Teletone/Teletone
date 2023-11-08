[To main documentation](00_MAIN.md)

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
    $update->answer('Your message: '.$update->text);
});
```

## command

Fires when the command is called

Params:

- **text** - Command text
- **callback** - Сallback for call
- **regex** - Sets true to check command by regex, then text should be a regular expression. It will only check the command text without parameters

Examples:

```php
// Use: /num
$r->command('num', static function($update) {
    $update->answer(mt_rand(1, 100));
});

// Parameters
// Use: /num param1 param2
$r->command('num', static function($update) {
    $update->answer('Count params: '.count($update->params));
    $update->answer('Param1: '.$update->params[0]);
    $update->answer('Param2: '.$update->params[1]);
});

// Use: /num1 /num5
$r->command('/num[0-9]/', static function($update) {
    $update->answer('ok');
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
- **regex** - Sets true to check callback by regex, then text should be a regular expression

Examples:

```php
$r->callbackQuery('click1', static function($update) {
    $update->answerCallback('clicked', [ 'show_alert' => true ]);
});

$r->callbackQuery('click2', static function($update) {
    $update->answerCallback(); // Will send an empty response so that the inline button is no longer in the loading state
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
    echo 'new member: '.$update->from->id."\n";
}, Teletone\Statuses::MEMBER);
```

[Next chapter: QRF](03_QRF.md)