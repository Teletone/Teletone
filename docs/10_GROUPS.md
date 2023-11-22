[To main documentation](00_MAIN.md)

# Groups

By default, Teletone does not process messages from group chats

To override this behavior, add the **all_groups** option:

```php
$bot = new Teletone\Bot($bot_token, [
    'all_groups' => true
]);
```

For routes in which **for_groups** is set to true, messages will in any case be processed only for the group

Example:

```php
use Teletone\Types;

$r->message(NULL, static function($u) {
    $u->answer('group_id: '.$u->chat->id);
}, false, Types::TEXT, true);
```

This is how you can delete service messages about joining and leaving a group:

```php
$r->message(NULL, static function($u) {
    $u->delete();
}, false, Types::LEFT_CHAT_PARTICIPANT|Types::NEW_CHAT_PARTICIPANT, true);
```

[Next chapter: Pre-handlers](11_PRE_HANDLERS.md.md)