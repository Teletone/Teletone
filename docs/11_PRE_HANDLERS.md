[To main documentation](00_MAIN.md)

# Pre-handlers

This feature is from functional programming and is quite working to this day

Using the router function **registerBeforeFunc**, you can register a handler that will be called before calling the event handler, and if the function returns false, then processing is not performed

Registration of the following pre-handlers is available: **any, command, message, callback_query, chat_member**

Example:

```php
$r->registerBeforeFunc('message', static function($u) {
    $u->answer('before message');
    return false;
});
```

This is very useful against DDoS attacks and flooding of the bot